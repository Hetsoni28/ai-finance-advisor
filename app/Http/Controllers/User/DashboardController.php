<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Services\FinancialStabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Personal dashboard. Every number here comes from the user's own records;
 * nothing is estimated or randomised.
 */
class DashboardController extends Controller
{
    public function index(FinancialStabilityService $stabilityService)
    {
        $userId = Auth::id();
        abort_unless($userId, 403);

        $now = now();
        $windowStart = $now->copy()->startOfMonth()->subMonths(5);

        /* ---------- Last 6 months, by transaction date ---------- */
        $incomeByMonth = Income::where('user_id', $userId)
            ->where('income_date', '>=', $windowStart)
            ->selectRaw("DATE_FORMAT(income_date, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')->pluck('total', 'ym');

        $expenseByMonth = Expense::where('user_id', $userId)
            ->where('expense_date', '>=', $windowStart)
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')->pluck('total', 'ym');

        $months = $monthlyIncome = $monthlyExpense = [];
        for ($i = 0; $i < 6; $i++) {
            $m = $windowStart->copy()->addMonths($i);
            $months[] = $m->format('M Y');
            $monthlyIncome[] = (float) ($incomeByMonth[$m->format('Y-m')] ?? 0);
            $monthlyExpense[] = (float) ($expenseByMonth[$m->format('Y-m')] ?? 0);
        }

        $thisMonthIncome  = end($monthlyIncome);
        $thisMonthExpense = end($monthlyExpense);
        $lastMonthIncome  = $monthlyIncome[4];
        $lastMonthExpense = $monthlyExpense[4];

        /* ---------- All-time totals ---------- */
        $totalIncome  = (float) Income::where('user_id', $userId)->sum('amount');
        $totalExpense = (float) Expense::where('user_id', $userId)->sum('amount');

        /* ---------- Spending by category (last 6 months) ---------- */
        $categories = Expense::where('user_id', $userId)
            ->where('expense_date', '>=', $windowStart)
            ->select(DB::raw("COALESCE(NULLIF(category, ''), 'Uncategorized') as category"), DB::raw('SUM(amount) as total'))
            ->groupBy('category')->orderByDesc('total')->limit(6)->get();

        /* ---------- Recent transactions (income + expenses merged) ---------- */
        $recent = Income::where('user_id', $userId)->latest('income_date')->latest('id')->limit(8)->get()
            ->map(fn ($r) => (object) [
                'type' => 'income', 'title' => $r->source ?: 'Income', 'category' => $r->category ?: 'Income',
                'amount' => (float) $r->amount, 'date' => Carbon::parse($r->income_date),
                'edit' => route('user.incomes.edit', $r), 'family' => (bool) $r->family_id,
            ])
            ->concat(Expense::where('user_id', $userId)->latest('expense_date')->latest('id')->limit(8)->get()
                ->map(fn ($r) => (object) [
                    'type' => 'expense', 'title' => $r->title ?: 'Expense', 'category' => $r->category ?: 'Uncategorized',
                    'amount' => (float) $r->amount, 'date' => Carbon::parse($r->expense_date),
                    'edit' => route('user.expenses.edit', $r), 'family' => (bool) $r->family_id,
                ]))
            ->sortByDesc(fn ($r) => $r->date->timestamp)
            ->take(8)->values();

        /* ---------- Health score (real formula), goals, alerts ---------- */
        $stability = $stabilityService->calculate($userId);

        $goals = FinancialGoal::where('user_id', $userId)
            ->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', 'completed'))
            ->orderBy('target_date')->limit(4)->get();

        $unreadAlerts = Alert::where('user_id', $userId)->where('is_read', false)->count();

        $topSource = Income::where('user_id', $userId)
            ->select('source', DB::raw('SUM(amount) as total'))
            ->groupBy('source')->orderByDesc('total')->first();

        return view('user.dashboard.index', compact(
            'months', 'monthlyIncome', 'monthlyExpense',
            'thisMonthIncome', 'thisMonthExpense', 'lastMonthIncome', 'lastMonthExpense',
            'totalIncome', 'totalExpense', 'categories', 'recent',
            'stability', 'goals', 'unreadAlerts', 'topSource'
        ));
    }
}
