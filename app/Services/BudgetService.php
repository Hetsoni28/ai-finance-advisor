<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Alert;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\User;
use App\Services\Ai\AiInsightService;
use App\Services\Ai\GeminiService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Monthly category budgets: progress for the current month, suggested limits from past spending,
 * and AI-written alerts when spending crosses 80% and 100% of a limit (each sent once a month).
 */
final class BudgetService
{
    public const ICONS = [
        'Food' => 'fa-utensils', 'Travel' => 'fa-plane', 'Bills' => 'fa-file-invoice',
        'Shopping' => 'fa-bag-shopping', 'Health' => 'fa-heart-pulse', 'Others' => 'fa-ellipsis',
    ];

    public function __construct(private GeminiService $gemini)
    {
    }

    /**
     * One row per expense category (budgeted or not) for the current month.
     *
     * @return Collection<int, array{category: string, icon: string, budget: ?Budget, limit: ?float, spent: float, pct: ?int,
     *                     remaining: ?float, perDay: ?float, projected: float, suggested: ?float, status: string}>
     */
    public function overview(User $user): Collection
    {
        $now = now();
        $daysIn = (int) $now->format('t');
        $day = (int) $now->format('j');
        $daysLeft = $daysIn - $day + 1; // including today

        $budgets = Budget::where('user_id', $user->id)->get()->keyBy('category');
        $spent = $this->spentThisMonth($user);
        $avg = $this->threeMonthAverage($user);

        $categories = collect(AiInsightService::EXPENSE_CATEGORIES)
            ->merge($budgets->keys())->merge(array_keys($spent))->unique()->values();

        return $categories->map(function (string $cat) use ($budgets, $spent, $avg, $day, $daysIn, $daysLeft) {
            $b = $budgets->get($cat);
            $s = (float) ($spent[$cat] ?? 0);
            $limit = $b ? (float) $b->monthly_limit : null;
            $pct = $limit ? $this->percent($s, $limit) : null;
            $remaining = $limit !== null ? $limit - $s : null;
            $projected = $day > 0 ? round($s / $day * $daysIn) : $s;

            $status = 'none';
            if ($limit) {
                $status = $pct >= 100 ? 'over' : ($pct >= 80 ? 'warn' : ($projected > $limit ? 'pace' : 'ok'));
            }

            return [
                'category'  => $cat,
                'icon'      => self::ICONS[$cat] ?? 'fa-tag',
                'budget'    => $b,
                'limit'     => $limit,
                'spent'     => $s,
                'pct'       => $pct,
                'remaining' => $remaining,
                'perDay'    => $remaining !== null && $remaining > 0 ? floor($remaining / max(1, $daysLeft)) : null,
                'projected' => $projected,
                'suggested' => isset($avg[$cat]) ? $this->roundUp($avg[$cat]) : null,
                'status'    => $status,
                'daysLeft'  => $daysLeft,
            ];
        });
    }

    /** Save (create or update) the limit for one category. Raising a limit re-arms its alerts. */
    public function save(User $user, string $category, float $limit): Budget
    {
        $budget = Budget::firstOrNew(['user_id' => $user->id, 'category' => $category]);
        $raised = !$budget->exists || $limit > (float) $budget->monthly_limit;
        $budget->monthly_limit = $limit;
        if ($raised) {
            $budget->alert_level = 0;
            $budget->alert_month = null;
        }
        $budget->save();

        // If the new limit is already crossed, say so straight away.
        $this->checkAndAlert($user, $category);

        return $budget;
    }

    /**
     * Called after an expense is added or changed. Sends an alert the first time this month that
     * spending in the category crosses 80% or 100% of its limit. Never throws.
     */
    public function checkAndAlert(User $user, ?string $category): ?Alert
    {
        if (!$category) {
            return null;
        }

        try {
            $budget = Budget::where('user_id', $user->id)->where('category', $category)->first();
            if (!$budget || $budget->monthly_limit <= 0) {
                return null;
            }

            $month = now()->format('Y-m');
            $alreadySent = $budget->alert_month === $month ? (int) $budget->alert_level : 0;

            $spent = (float) ($this->spentThisMonth($user)[$category] ?? 0);
            $pct = $this->percent($spent, (float) $budget->monthly_limit);
            $level = $pct >= 100 ? 100 : ($pct >= 80 ? 80 : 0);

            if ($level === 0 || $level <= $alreadySent) {
                return null;
            }

            $daysLeft = (int) now()->format('t') - (int) now()->format('j') + 1;
            $message = $this->writeAlert($user, $category, (float) $budget->monthly_limit, $spent, $pct, $daysLeft);

            $budget->update(['alert_level' => $level, 'alert_month' => $month]);

            return Alert::create([
                'user_id' => $user->id,
                'type'    => $level >= 100 ? 'risk' : 'warning',
                'message' => $message,
            ]);
        } catch (Throwable $e) {
            Log::warning('Budget alert failed: ' . $e->getMessage());
            return null;
        }
    }

    /** Short alert written by Gemini from the real figures; falls back to a template. Max 240 chars. */
    private function writeAlert(User $user, string $cat, float $limit, float $spent, int $pct, int $daysLeft): string
    {
        $inr = fn ($v) => '₹' . inr_number(abs($v), 0);
        $over = $spent - $limit;

        $template = $pct >= 100
            ? "You've gone over your {$cat} budget: {$inr($spent)} spent against a {$inr($limit)} limit ({$inr($over)} over) with {$daysLeft} days left this month."
            : "You've used {$pct}% of your {$cat} budget ({$inr($spent)} of {$inr($limit)}) with {$daysLeft} days left. About {$inr(($limit - $spent) / max(1, $daysLeft))} a day keeps you on track.";

        $prompt = "You are FinanceAI, a friendly personal finance advisor in India. Write ONE short budget alert for the user.\n"
            . "Facts (use exactly these, invent nothing): category {$cat}; monthly limit {$inr($limit)}; spent so far {$inr($spent)}; "
            . "that is {$pct}% of the limit; {$daysLeft} days left in the month"
            . ($pct >= 100 ? "; over the limit by {$inr($over)}" : "; left to spend {$inr($limit - $spent)}") . ".\n"
            . "Rules: one or two sentences, at most 200 characters, plain text (no markdown, no emoji, no greeting). Mention the percentage and days left, "
            . "and give one practical tip for {$cat} spending. Use the ₹ sign.";

        $ai = $this->gemini->complete($prompt, 1024, 0.5, 6);
        $text = $ai['success'] ? trim(preg_replace('/\s+/', ' ', preg_replace('/[*_#`]+/', '', $ai['content']))) : '';

        // Accept the AI text only if it is short enough and mentions the real percentage.
        if ($text === '' || mb_strlen($text) > 240 || !str_contains($text, (string) $pct)) {
            $text = $template;
        }

        return Str::limit($text, 250, '');
    }

    /** @return array<string, float> category => spent this calendar month */
    private function spentThisMonth(User $user): array
    {
        return Expense::where('user_id', $user->id)
            ->whereBetween('expense_date', [now()->startOfMonth(), now()->endOfDay()])
            ->selectRaw("COALESCE(NULLIF(category, ''), 'Others') as category, SUM(amount) as total")
            ->groupBy('category')->pluck('total', 'category')->map(fn ($v) => (float) $v)->all();
    }

    /** @return array<string, float> category => average monthly spend over the previous 3 full months (months with spending only) */
    private function threeMonthAverage(User $user): array
    {
        $rows = Expense::where('user_id', $user->id)
            ->whereBetween('expense_date', [now()->subMonthsNoOverflow(3)->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])
            ->selectRaw("COALESCE(NULLIF(category, ''), 'Others') as category, DATE_FORMAT(expense_date, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('category', 'ym')->get();

        return $rows->groupBy('category')->map(fn ($r) => (float) $r->avg('total'))->all();
    }

    /** Whole percent used, rounded down — after removing float noise (1150/1000*100 = 114.999…). */
    private function percent(float $spent, float $limit): int
    {
        return (int) floor(round($spent / $limit * 100, 6));
    }

    /** Round a suggestion up to a friendly number (nearest ₹500, or ₹100 below ₹2,000). */
    private function roundUp(float $v): float
    {
        $step = $v < 2000 ? 100 : 500;
        return (float) (ceil($v / $step) * $step);
    }
}
