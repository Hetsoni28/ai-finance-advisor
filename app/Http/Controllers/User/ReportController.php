<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use App\Services\Ai\AiInsightService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * Personal reports for a chosen period. Every figure is read from the user's
 * own personal records; there is no demo or placeholder data.
 */
class ReportController extends Controller
{
    private const PRESETS = [
        'this_month' => 'This month',
        'last_month' => 'Last month',
        '3m'         => 'Last 3 months',
        '6m'         => 'Last 6 months',
        'ytd'        => 'This year',
        '12m'        => 'Last 12 months',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        return view('user.reports.index', $this->buildReport($request));
    }

    /**
     * "Explain this period": the AI describes what changed, why and what to do, using the same
     * figures the Reports page shows for the selected range.
     */
    public function explain(Request $request, AiInsightService $ai): JsonResponse
    {
        try {
            $d = $this->buildReport($request);
            $catMap = fn ($rows) => $rows->mapWithKeys(fn ($c) => [$c->category => (float) $c->total])->all();

            $prevExpenseCategories = Expense::where('user_id', Auth::id())->where('is_personal', true)->whereNull('family_id')
                ->whereBetween('expense_date', [$d['prevFrom'], $d['prevTo']])
                ->select(DB::raw("COALESCE(NULLIF(category, ''), 'Uncategorized') as category"), DB::raw('SUM(amount) as total'))
                ->groupBy('category')->orderByDesc('total')->get();

            $result = $ai->explainPeriod(Auth::user(), [
                'label'                 => $d['presets'][$d['preset']] ?? 'Custom period',
                'from'                  => $d['from']->format('d M Y'),
                'to'                    => $d['to']->format('d M Y'),
                'totalIncome'           => $d['totalIncome'],
                'totalExpense'          => $d['totalExpense'],
                'prevIncome'            => $d['prevIncome'],
                'prevExpense'           => $d['prevExpense'],
                'expenseCategories'     => $catMap($d['expenseCategories']),
                'prevExpenseCategories' => $catMap($prevExpenseCategories),
                'monthly'               => $d['monthly'],
                'largestExpenses'       => $d['largestExpenses']->mapWithKeys(fn ($e) => [($e->title ?: 'Expense') . ' (' . $e->expense_date->format('d M') . ')' => (float) $e->amount])->all(),
            ]);

            return response()->json($result);
        } catch (Throwable $e) {
            Log::warning('Report explain failed: ' . $e->getMessage());
            return response()->json(['html' => "<p>The explanation isn't available right now. Please try again in a moment.</p>", 'source' => 'error']);
        }
    }

    /** All figures for the selected period (shared by the page and the AI explanation). */
    private function buildReport(Request $request): array
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        [$from, $to, $preset] = $this->resolveRange($request);

        // Previous period of equal length, for comparison
        $days = $from->diffInDays($to) + 1;
        $prevTo = $from->copy()->subDay()->endOfDay();
        $prevFrom = $prevTo->copy()->subDays($days - 1)->startOfDay();

        $income = fn () => Income::where('user_id', $user->id)->where('is_personal', true)->whereNull('family_id');
        $expense = fn () => Expense::where('user_id', $user->id)->where('is_personal', true)->whereNull('family_id');

        $totalIncome  = (float) $income()->whereBetween('income_date', [$from, $to])->sum('amount');
        $totalExpense = (float) $expense()->whereBetween('expense_date', [$from, $to])->sum('amount');
        $prevIncome   = (float) $income()->whereBetween('income_date', [$prevFrom, $prevTo])->sum('amount');
        $prevExpense  = (float) $expense()->whereBetween('expense_date', [$prevFrom, $prevTo])->sum('amount');
        $incomeCount  = $income()->whereBetween('income_date', [$from, $to])->count();
        $expenseCount = $expense()->whereBetween('expense_date', [$from, $to])->count();

        // Month-by-month series covering the whole range
        $incByMonth = $income()->whereBetween('income_date', [$from, $to])
            ->selectRaw("DATE_FORMAT(income_date, '%Y-%m') as ym, SUM(amount) as total")->groupBy('ym')->pluck('total', 'ym');
        $expByMonth = $expense()->whereBetween('expense_date', [$from, $to])
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as ym, SUM(amount) as total")->groupBy('ym')->pluck('total', 'ym');
        $monthly = [];
        for ($m = $from->copy()->startOfMonth(); $m->lte($to); $m->addMonth()) {
            $key = $m->format('Y-m');
            $monthly[] = ['label' => $m->format('M Y'), 'income' => (float) ($incByMonth[$key] ?? 0), 'expense' => (float) ($expByMonth[$key] ?? 0)];
        }

        $expenseCategories = $expense()->whereBetween('expense_date', [$from, $to])
            ->select(DB::raw("COALESCE(NULLIF(category, ''), 'Uncategorized') as category"), DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as n'))
            ->groupBy('category')->orderByDesc('total')->get();
        $incomeCategories = $income()->whereBetween('income_date', [$from, $to])
            ->select(DB::raw("COALESCE(NULLIF(category, ''), 'Uncategorized') as category"), DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as n'))
            ->groupBy('category')->orderByDesc('total')->get();

        $largestExpenses = $expense()->whereBetween('expense_date', [$from, $to])->orderByDesc('amount')->limit(5)->get();
        $largestIncome   = $income()->whereBetween('income_date', [$from, $to])->orderByDesc('amount')->limit(5)->get();

        return [
            'from' => $from, 'to' => $to, 'preset' => $preset, 'presets' => self::PRESETS,
            'prevFrom' => $prevFrom, 'prevTo' => $prevTo,
            'totalIncome' => $totalIncome, 'totalExpense' => $totalExpense,
            'prevIncome' => $prevIncome, 'prevExpense' => $prevExpense,
            'incomeCount' => $incomeCount, 'expenseCount' => $expenseCount,
            'monthly' => $monthly,
            'expenseCategories' => $expenseCategories, 'incomeCategories' => $incomeCategories,
            'largestExpenses' => $largestExpenses, 'largestIncome' => $largestIncome,
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function resolveRange(Request $request): array
    {
        $now = now();
        $preset = $request->input('range');

        if ($request->filled('from') || $request->filled('to')) {
            try {
                $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : $now->copy()->subMonths(5)->startOfMonth();
                $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : $now->copy()->endOfDay();
                if ($from->gt($to)) { [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()]; }
                return [$from, $to, 'custom'];
            } catch (Throwable $e) {
                // fall through to the default preset on invalid input
            }
        }

        switch ($preset) {
            case 'this_month': return [$now->copy()->startOfMonth(), $now->copy()->endOfDay(), $preset];
            case 'last_month': return [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth(), $preset];
            case '3m':         return [$now->copy()->subMonths(2)->startOfMonth(), $now->copy()->endOfDay(), $preset];
            case 'ytd':        return [$now->copy()->startOfYear(), $now->copy()->endOfDay(), $preset];
            case '12m':        return [$now->copy()->subMonths(11)->startOfMonth(), $now->copy()->endOfDay(), $preset];
            default:           return [$now->copy()->subMonths(5)->startOfMonth(), $now->copy()->endOfDay(), '6m'];
        }
    }
}
