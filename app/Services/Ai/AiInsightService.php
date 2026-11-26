<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Small AI features built on Gemini, each with a rule-based fallback so they always return something:
 *  - monthSummary()   : 2–3 sentences about the user's month for the dashboard
 *  - suggestCategory(): picks a category from an entry title ("Swiggy order" -> Food)
 *  - explainPeriod()  : explains a Reports period (what changed, why, what to do)
 *
 * Every number given to Gemini comes from the user's own records, and results are cached against a
 * fingerprint of those numbers so the same data is never sent twice and new entries refresh the text.
 */
final class AiInsightService
{
    public const EXPENSE_CATEGORIES = ['Food', 'Travel', 'Bills', 'Shopping', 'Health', 'Others'];
    public const INCOME_CATEGORIES  = ['Salary', 'Freelance', 'Investments', 'Business', 'Gifts', 'Others'];

    /** Common Indian merchants and words → category. Checked before asking Gemini (instant, free). */
    private const KEYWORDS = [
        'expense' => [
            'Food'     => ['swiggy', 'zomato', 'restaurant', 'cafe', 'coffee', 'starbucks', 'pizza', 'dominos', 'domino', 'mcdonald', 'kfc', 'burger',
                           'grocery', 'groceries', 'bigbasket', 'blinkit', 'zepto', 'instamart', 'dmart', 'milk', 'vegetables', 'fruits', 'lunch',
                           'dinner', 'breakfast', 'tea', 'chai', 'snacks', 'bakery', 'food', 'meal', 'canteen', 'mess', 'tiffin', 'dhaba', 'biryani'],
            'Travel'   => ['uber', 'ola', 'rapido', 'metro', 'train', 'irctc', 'flight', 'indigo', 'air india', 'vistara', 'bus', 'redbus',
                           'petrol', 'diesel', 'fuel', 'cab', 'taxi', 'auto', 'rickshaw', 'toll', 'fastag', 'parking', 'makemytrip', 'goibibo',
                           'hotel', 'oyo', 'trip', 'travel', 'ticket'],
            'Bills'    => ['electricity', 'jio', 'airtel', 'vodafone', 'bsnl', 'recharge', 'wifi', 'broadband', 'internet', 'rent',
                           'water bill', 'gas', 'lpg', 'cylinder', 'dth', 'tata play', 'emi', 'loan', 'insurance', 'lic', 'premium', 'netflix',
                           'hotstar', 'spotify', 'prime', 'youtube premium', 'subscription', 'maintenance', 'bill', 'fees', 'tuition'],
            'Shopping' => ['amazon', 'flipkart', 'myntra', 'meesho', 'ajio', 'nykaa', 'clothes', 'shirt', 'jeans', 'shoes', 'mall', 'croma',
                           'reliance digital', 'electronics', 'phone', 'laptop', 'headphones', 'furniture', 'ikea', 'decathlon', 'shopping'],
            'Health'   => ['pharmacy', 'medicine', 'medicines', 'apollo', 'medplus', 'pharmeasy', '1mg', 'netmeds', 'doctor', 'hospital',
                           'clinic', 'gym', 'cult', 'dental', 'dentist', 'lab test', 'blood test', 'checkup', 'health', 'physio', 'yoga'],
        ],
        'income' => [
            'Salary'      => ['salary', 'payroll', 'stipend', 'wages', 'paycheck', 'pay slip', 'payslip', 'ctc'],
            'Freelance'   => ['freelance', 'upwork', 'fiverr', 'client', 'project', 'consulting', 'invoice', 'contract', 'gig'],
            'Investments' => ['dividend', 'interest', 'mutual fund', 'sip', 'stock', 'stocks', 'shares', 'fd', 'fixed deposit', 'returns',
                              'capital gain', 'zerodha', 'groww', 'bond', 'ppf'],
            'Business'    => ['business', 'shop', 'sales', 'revenue', 'store', 'order payment', 'customer'],
            'Gifts'       => ['gift', 'birthday', 'wedding', 'shagun', 'diwali', 'rakhi', 'from mom', 'from dad', 'pocket money', 'bonus gift'],
        ],
    ];

    public function __construct(private GeminiService $gemini)
    {
    }

    /* ------------------------------------------------------------------ */
    /*  Dashboard: AI summary of the month                                  */
    /* ------------------------------------------------------------------ */

    /** @return array{text: string, source: string} source = 'gemini' | 'rules' */
    public function monthSummary(User $user): array
    {
        $d = $this->monthData($user);

        if ($d['entries_this'] === 0 && $d['entries_last'] === 0) {
            return [
                'text'   => "I don't have any income or expenses from this month or last month to look at yet. Add a few entries and I'll summarise your month here.",
                'source' => 'rules',
            ];
        }

        $key = 'ai.summary.v2.' . $user->id . '.' . md5(json_encode($d));

        return Cache::remember($key, now()->addHours(6), function () use ($d, $user) {
            $prompt = "You are FinanceAI, a friendly personal finance advisor for people in India.\n"
                . "Write a summary of the user's month so far in 2 or 3 short sentences (at most 60 words).\n"
                . "Mention: how spending compares with last month, the biggest spending category, and how much of their income they kept compared with the usual 20% savings target.\n"
                . "Rules: plain text only — no markdown, no headings, no lists, no greeting. Write amounts with the ₹ sign exactly as written below (e.g. ₹40,000), never the word \"rupees\". Do not invent any numbers. Speak directly to the user (\"you\").\n\n"
                . "User's first name: " . Str::of($user->name)->before(' ') . "\n"
                . $this->describeMonth($d);

            $ai = $this->gemini->complete($prompt, 2048, 0.4, 12);
            $text = $ai['success'] ? $this->wholeSentences($this->plain($ai['content'])) : '';

            return $text !== ''
                ? ['text' => $text, 'source' => 'gemini']
                : ['text' => $this->ruleSummary($d), 'source' => 'rules'];
        });
    }

    private function monthData(User $user): array
    {
        $now = now();
        $thisFrom = $now->copy()->startOfMonth();
        $lastFrom = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastTo   = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $sum = fn ($model, $col, $from, $to) => (float) $model::where('user_id', $user->id)->whereBetween($col, [$from, $to])->sum('amount');
        $cnt = fn ($model, $col, $from, $to) => (int) $model::where('user_id', $user->id)->whereBetween($col, [$from, $to])->count();
        $cats = fn ($from, $to) => Expense::where('user_id', $user->id)->whereBetween('expense_date', [$from, $to])
            ->selectRaw("COALESCE(NULLIF(category, ''), 'Others') as category, SUM(amount) as total")
            ->groupBy('category')->orderByDesc('total')->limit(3)->pluck('total', 'category')
            ->map(fn ($v) => (float) $v)->all();

        return [
            'month'        => $now->format('F Y'),
            'day'          => (int) $now->format('j'),
            'days_in'      => (int) $now->format('t'),
            'income_this'  => $sum(Income::class, 'income_date', $thisFrom, $now),
            'expense_this' => $sum(Expense::class, 'expense_date', $thisFrom, $now),
            'income_last'  => $sum(Income::class, 'income_date', $lastFrom, $lastTo),
            'expense_last' => $sum(Expense::class, 'expense_date', $lastFrom, $lastTo),
            'cats_this'    => $cats($thisFrom, $now),
            'cats_last'    => $cats($lastFrom, $lastTo),
            'entries_this' => $cnt(Income::class, 'income_date', $thisFrom, $now) + $cnt(Expense::class, 'expense_date', $thisFrom, $now),
            'entries_last' => $cnt(Income::class, 'income_date', $lastFrom, $lastTo) + $cnt(Expense::class, 'expense_date', $lastFrom, $lastTo),
        ];
    }

    private function describeMonth(array $d): string
    {
        $lines = [
            "Month: {$d['month']} (day {$d['day']} of {$d['days_in']})",
            'Income this month: ' . $this->inr($d['income_this']),
            'Spending this month: ' . $this->inr($d['expense_this']),
            'Income last month: ' . $this->inr($d['income_last']),
            'Spending last month: ' . $this->inr($d['expense_last']),
        ];
        if ($d['income_this'] > 0) {
            $lines[] = 'Kept this month: ' . $this->inr($d['income_this'] - $d['expense_this'])
                . ' (' . round(($d['income_this'] - $d['expense_this']) / $d['income_this'] * 100) . '% of income)';
        }
        if ($d['expense_last'] > 0) {
            $lines[] = 'Spending change vs last month: ' . $this->pct($d['expense_this'], $d['expense_last']);
        }
        if ($d['cats_this']) {
            $lines[] = 'Top spending categories this month: ' . $this->catList($d['cats_this']);
        }
        if ($d['cats_last']) {
            $lines[] = 'Top spending categories last month: ' . $this->catList($d['cats_last']);
        }
        return implode("\n", $lines);
    }

    private function ruleSummary(array $d): string
    {
        $parts = [];

        if ($d['expense_this'] > 0) {
            $s = 'You have spent ' . $this->inr($d['expense_this']) . ' so far in ' . $d['month'];
            if ($d['expense_last'] > 0) {
                $diff = $d['expense_this'] - $d['expense_last'];
                $s .= $diff > 0
                    ? ', ' . $this->inr($diff) . ' more than the whole of last month'
                    : ', ' . $this->inr(abs($diff)) . ' less than last month';
            }
            $parts[] = $s . '.';
            if ($d['cats_this']) {
                $top = array_key_first($d['cats_this']);
                $parts[] = "Your biggest category is {$top} at " . $this->inr($d['cats_this'][$top]) . '.';
            }
        } elseif ($d['income_this'] > 0) {
            $parts[] = "You haven't recorded any spending in {$d['month']} yet.";
        } else {
            $parts[] = "Nothing recorded for {$d['month']} yet — last month you brought in " . $this->inr($d['income_last'])
                . ' and spent ' . $this->inr($d['expense_last']) . '.';
        }

        if ($d['income_this'] > 0) {
            $rate = round(($d['income_this'] - $d['expense_this']) / $d['income_this'] * 100);
            $parts[] = $rate >= 20
                ? "You've kept {$rate}% of your income — above the usual 20% savings target."
                : ($rate >= 0
                    ? "You've kept {$rate}% of your income, below the usual 20% savings target."
                    : 'You are spending more than you earned this month.');
        }

        return implode(' ', $parts);
    }

    /* ------------------------------------------------------------------ */
    /*  Entry forms: suggest a category from the title                      */
    /* ------------------------------------------------------------------ */

    /** @return array{category: ?string, source: string} source = 'keywords' | 'gemini' | 'none' */
    public function suggestCategory(string $title, string $type): array
    {
        $type = $type === 'income' ? 'income' : 'expense';
        $title = trim(Str::limit($title, 120, ''));
        $lower = Str::lower($title);

        if (mb_strlen($lower) < 3) {
            return ['category' => null, 'source' => 'none'];
        }

        foreach (self::KEYWORDS[$type] as $category => $words) {
            foreach ($words as $w) {
                if (preg_match('/(^|[^a-z0-9])' . preg_quote($w, '/') . '([^a-z0-9]|$)/u', $lower)) {
                    return ['category' => $category, 'source' => 'keywords'];
                }
            }
        }

        $allowed = $type === 'income' ? self::INCOME_CATEGORIES : self::EXPENSE_CATEGORIES;

        return Cache::remember('ai.category.' . $type . '.' . md5($lower), now()->addDays(30), function () use ($title, $type, $allowed) {
            $prompt = "Classify this personal {$type} entry from India into exactly one category.\n"
                . 'Categories: ' . implode(', ', $allowed) . "\n"
                . 'Entry title: "' . str_replace('"', "'", $title) . "\"\n"
                . 'Answer with only the category name, nothing else.';

            $ai = $this->gemini->complete($prompt, 256, 0.0, 6);
            if ($ai['success']) {
                $answer = trim(preg_replace('/[^A-Za-z]/', '', $ai['content']));
                foreach ($allowed as $c) {
                    if (strcasecmp($answer, $c) === 0) {
                        return ['category' => $c, 'source' => 'gemini'];
                    }
                }
            }
            return ['category' => null, 'source' => 'none'];
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Reports: explain the selected period                                */
    /* ------------------------------------------------------------------ */

    /**
     * @param array $r keys: label, from, to, totalIncome, totalExpense, prevIncome, prevExpense,
     *                 expenseCategories (category=>total), prevExpenseCategories, monthly, largestExpenses (title=>amount)
     * @return array{html: string, source: string}
     */
    public function explainPeriod(User $user, array $r): array
    {
        if ($r['totalIncome'] <= 0 && $r['totalExpense'] <= 0) {
            return [
                'html'   => '<p>There are no income or expense entries in this period, so there is nothing to explain yet. Pick a different period or add some entries.</p>',
                'source' => 'rules',
            ];
        }

        $key = 'ai.explain.' . $user->id . '.' . md5(json_encode($r));

        return Cache::remember($key, now()->addHours(6), function () use ($r) {
            $prompt = "You are FinanceAI, a personal finance advisor for people in India. Explain the user's report for this period.\n"
                . "Write exactly three short sections with these bold labels: **What changed**, **Why**, **What to do**.\n"
                . "Each section is 1–2 short sentences or up to 3 bullets. At most 130 words in total.\n"
                . "Rules: use only the numbers below, write rupee amounts exactly as given, don't invent anything, speak to the user as \"you\". Markdown bold and bullets only — no headings, no tables.\n\n"
                . $this->describePeriod($r);

            $ai = $this->gemini->complete($prompt, 1500, 0.4, 15);
            $md = $ai['success'] ? trim($ai['content']) : '';

            return $md !== ''
                ? ['html' => $this->toHtml($md), 'source' => 'gemini']
                : ['html' => $this->toHtml($this->ruleExplanation($r)), 'source' => 'rules'];
        });
    }

    private function describePeriod(array $r): string
    {
        $lines = [
            "Period: {$r['label']} ({$r['from']} to {$r['to']})",
            'Income: ' . $this->inr($r['totalIncome']) . ' (previous period: ' . $this->inr($r['prevIncome']) . ')',
            'Spending: ' . $this->inr($r['totalExpense']) . ' (previous period: ' . $this->inr($r['prevExpense']) . ')',
        ];
        if ($r['prevExpense'] > 0) {
            $lines[] = 'Spending change: ' . $this->pct($r['totalExpense'], $r['prevExpense']);
        }
        if ($r['totalIncome'] > 0) {
            $lines[] = 'Kept: ' . $this->inr($r['totalIncome'] - $r['totalExpense'])
                . ' (' . round(($r['totalIncome'] - $r['totalExpense']) / $r['totalIncome'] * 100) . '% of income; usual target 20%)';
        }
        if ($r['expenseCategories']) {
            $lines[] = 'Spending by category this period: ' . $this->catList($r['expenseCategories']);
        }
        if ($r['prevExpenseCategories']) {
            $lines[] = 'Spending by category previous period: ' . $this->catList($r['prevExpenseCategories']);
        }
        if ($r['largestExpenses']) {
            $lines[] = 'Largest expenses: ' . $this->catList($r['largestExpenses']);
        }
        if (count($r['monthly']) > 1) {
            $lines[] = 'By month: ' . implode('; ', array_map(
                fn ($m) => "{$m['label']} in " . $this->inr($m['income']) . ' / out ' . $this->inr($m['expense']),
                $r['monthly']
            ));
        }
        return implode("\n", $lines);
    }

    private function ruleExplanation(array $r): string
    {
        $changed = 'You brought in ' . $this->inr($r['totalIncome']) . ' and spent ' . $this->inr($r['totalExpense']) . '.';
        if ($r['prevExpense'] > 0) {
            $changed .= ' Spending is ' . $this->pct($r['totalExpense'], $r['prevExpense']) . ' compared with the previous period (' . $this->inr($r['prevExpense']) . ').';
        }

        $why = 'There is no spending in this period.';
        if ($r['expenseCategories']) {
            $top = array_key_first($r['expenseCategories']);
            $share = $r['totalExpense'] > 0 ? round($r['expenseCategories'][$top] / $r['totalExpense'] * 100) : 0;
            $why = "{$top} is your biggest category at " . $this->inr($r['expenseCategories'][$top]) . " ({$share}% of spending).";
            $prevTop = $r['prevExpenseCategories'][$top] ?? null;
            if ($prevTop !== null && $prevTop > 0) {
                $why .= " It was " . $this->inr($prevTop) . ' in the previous period.';
            }
        }

        $todo = [];
        if ($r['totalIncome'] > 0) {
            $rate = round(($r['totalIncome'] - $r['totalExpense']) / $r['totalIncome'] * 100);
            $todo[] = $rate >= 20
                ? "You kept {$rate}% of your income — keep moving the surplus into savings or a goal."
                : "You kept {$rate}% of your income. Aim for 20% by trimming your biggest category first.";
        }
        if ($r['expenseCategories']) {
            $todo[] = 'Set a monthly limit for ' . array_key_first($r['expenseCategories']) . ' and check it each week.';
        }

        return "**What changed** — {$changed}\n\n**Why** — {$why}\n\n**What to do**\n" . implode("\n", array_map(fn ($t) => "- {$t}", $todo ?: ['Add your income for this period so the savings rate can be worked out.']));
    }

    /* ------------------------------------------------------------------ */
    /*  Receipt scanning: read a bill photo with Gemini vision              */
    /* ------------------------------------------------------------------ */

    /**
     * Reads a receipt/bill image and returns fields for the expense form. Nothing is saved — the
     * image is only held in memory for this request. Every value Gemini returns is re-checked here.
     *
     * @return array{ok: bool, message: string, fields?: array{amount: ?float, title: ?string, date: ?string, category: ?string}, notes?: string[]}
     */
    public function scanReceipt(string $imageData, string $mime): array
    {
        if (!$this->gemini->isAvailable()) {
            return ['ok' => false, 'message' => 'Receipt scanning needs the AI service, which isn\'t set up right now. Please fill in the details yourself.'];
        }

        $today = now()->format('Y-m-d');
        $prompt = "You are reading a photo of a shop bill, receipt or invoice, most likely from India.\n"
            . "Return ONLY a JSON object with exactly these keys:\n"
            . "{\"is_receipt\": true|false, \"amount\": number|null, \"merchant\": string|null, \"title\": string|null, \"date\": \"YYYY-MM-DD\"|null, \"category\": string|null, \"currency\": string|null}\n"
            . "Rules:\n"
            . "- amount: the final total actually paid (grand total / net payable, after tax and discounts). A plain number, no currency symbol or commas.\n"
            . "- merchant: the shop or company name as printed.\n"
            . "- title: a short description for an expense list, at most 40 characters, e.g. \"Swiggy order\", \"BigBasket groceries\", \"Apollo Pharmacy medicines\".\n"
            . "- date: the bill date in YYYY-MM-DD. Indian bills usually write dates as DD/MM/YYYY. Use null if no date is visible. Today is {$today}.\n"
            . '- category: exactly one of ' . implode(', ', self::EXPENSE_CATEGORIES) . ".\n"
            . "- currency: the currency code shown, e.g. INR.\n"
            . "- If the image is not a bill or receipt, set is_receipt to false and everything else to null.\n"
            . "- Never guess a number you cannot read; use null instead.";

        $ai = $this->gemini->vision($prompt, $imageData, $mime, 1024, 18);
        if (!$ai['success']) {
            return ['ok' => false, 'message' => 'The AI couldn\'t read the bill right now (it may be busy). Please try again in a moment, or fill in the details yourself.'];
        }

        $data = json_decode(trim(preg_replace('/^```(?:json)?|```$/m', '', $ai['content'])), true);
        if (!is_array($data)) {
            return ['ok' => false, 'message' => 'The AI\'s answer couldn\'t be understood. Please try a clearer photo or fill in the details yourself.'];
        }
        if (empty($data['is_receipt'])) {
            return ['ok' => false, 'message' => 'This doesn\'t look like a bill or receipt. Try a photo of the whole bill, flat and in good light.'];
        }

        $notes = [];

        // Amount: positive, sane, at most 2 decimals.
        $amount = is_numeric($data['amount'] ?? null) ? round((float) $data['amount'], 2) : null;
        if ($amount !== null && ($amount <= 0 || $amount > 99999999)) { $amount = null; }
        if ($amount === null) { $notes[] = 'The total wasn\'t clear — please enter the amount.'; }
        $currency = strtoupper(trim((string) ($data['currency'] ?? 'INR')));
        if ($amount !== null && $currency !== '' && !in_array($currency, ['INR', 'RS', 'RS.', '₹'], true)) {
            $notes[] = "The bill looks like it's in {$currency}, not rupees — check the amount.";
        }

        // Date: valid, not in the future, not absurdly old.
        $date = null;
        if (!empty($data['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $data['date'])) {
            try {
                $d = \Carbon\Carbon::createFromFormat('Y-m-d', $data['date'])->startOfDay();
                if ($d->lte(now()) && $d->gte(now()->subYears(5))) { $date = $d->format('Y-m-d'); }
            } catch (\Throwable $e) {
                $date = null;
            }
        }
        if ($date === null) { $notes[] = 'No valid date found — today\'s date is kept.'; }

        // Title: short and clean, fall back to the merchant name.
        $title = trim((string) ($data['title'] ?? '')) ?: trim((string) ($data['merchant'] ?? ''));
        $title = $title !== '' ? Str::limit(preg_replace('/\s+/', ' ', strip_tags($title)), 60, '') : null;

        // Category: must be one of ours; otherwise reuse the keyword/AI suggester on the title.
        $category = null;
        foreach (self::EXPENSE_CATEGORIES as $c) {
            if (strcasecmp(trim((string) ($data['category'] ?? '')), $c) === 0) { $category = $c; }
        }
        if ($category === null && $title) {
            $category = $this->suggestCategory($title, 'expense')['category'];
        }

        $found = count(array_filter([$amount, $title, $date, $category], fn ($v) => $v !== null));

        return [
            'ok'      => $found > 0,
            'message' => $found > 0 ? 'Filled in from your bill — check the details before saving.' : 'The AI couldn\'t read any details from this photo. Try a sharper photo of the whole bill.',
            'fields'  => ['amount' => $amount, 'title' => $title, 'date' => $date, 'category' => $category],
            'notes'   => $notes,
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function inr(float $v): string
    {
        return ($v < 0 ? '-' : '') . '₹' . inr_number(abs($v), 0);
    }

    private function pct(float $now, float $before): string
    {
        $p = round(($now - $before) / $before * 100);
        return $p === 0.0 ? 'about the same' : ($p > 0 ? "up {$p}%" : 'down ' . abs($p) . '%');
    }

    private function catList(array $cats): string
    {
        return implode(', ', array_map(fn ($c, $v) => "{$c} " . $this->inr((float) $v), array_keys($cats), $cats));
    }

    /** Strip any markdown Gemini adds despite instructions, keep it one paragraph. */
    private function plain(string $text): string
    {
        $text = preg_replace('/[*_#`>]+/', '', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Keep only complete sentences, so a summary never ends mid-sentence ("…₹4,420. You saved").
     * Returns '' when there is no complete sentence, and the caller then uses the rule-based text.
     */
    private function wholeSentences(string $text, int $maxChars = 600): string
    {
        $text = trim($text);
        if (mb_strlen($text) > $maxChars) {
            $text = mb_substr($text, 0, $maxChars);
        }
        if (preg_match('/[.!?]["”’)]*$/u', $text)) {
            return $text;
        }
        // Cut back to the last sentence end. A dot inside an amount (₹1,234.50) is not a sentence end.
        if (preg_match('/^.*[.!?]["”’)]*(?=\s)/us', $text, $m)) {
            return trim($m[0]);
        }
        return '';
    }

    private function toHtml(string $markdown): string
    {
        // Raw HTML from the model is stripped and unsafe links are blocked.
        return Str::markdown($markdown, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }
}
