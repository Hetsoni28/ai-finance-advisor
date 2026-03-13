<?php

namespace App\Services;

use App\Models\Family;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class FamilyDashboardService
{
    public function build(Family $family): array
    {
        // 🔥 Short cache to prevent stale dashboard confusion
        return Cache::remember(
            "family_dashboard_{$family->id}",
            now()->addMinute(),
            fn () => $this->generate($family)
        );
    }

    private function generate(Family $family): array
    {
        $familyId = $family->id;

        /*
        |--------------------------------------------------------------------------
        | STRICT TOTALS
        |--------------------------------------------------------------------------
        */

        $totalIncome = (float) Income::query()
            ->where('family_id', $familyId)
            ->where('is_personal', false)
            ->sum('amount');

        $totalExpense = (float) Expense::query()
            ->where('family_id', $familyId)
            ->where('is_personal', false)
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | MONTHLY TREND (USING month/year)
        |--------------------------------------------------------------------------
        */

        $incomeMonthly = Income::query()
            ->selectRaw('year, month, SUM(amount) as total')
            ->where('family_id', $familyId)
            ->where('is_personal', false)
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($r) => sprintf('%04d-%02d', $r->year, $r->month));

        $expenseMonthly = Expense::query()
            ->selectRaw('year, month, SUM(amount) as total')
            ->where('family_id', $familyId)
            ->where('is_personal', false)
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($r) => sprintf('%04d-%02d', $r->year, $r->month));

        $months = [];
        $incomeTrend = [];
        $expenseTrend = [];

        for ($i = 5; $i >= 0; $i--) {

            $date = Carbon::now()->subMonths($i);
            $key  = $date->format('Y-m');

            $months[] = $date->format('M');

            $incomeTrend[]  = (float) ($incomeMonthly[$key]->total ?? 0);
            $expenseTrend[] = (float) ($expenseMonthly[$key]->total ?? 0);
        }

        /*
        |--------------------------------------------------------------------------
        | GROWTH
        |--------------------------------------------------------------------------
        */

        $thisKey = now()->format('Y-m');
        $lastKey = now()->subMonth()->format('Y-m');

        $thisIncome  = (float) ($incomeMonthly[$thisKey]->total ?? 0);
        $lastIncome  = (float) ($incomeMonthly[$lastKey]->total ?? 0);

        $thisExpense = (float) ($expenseMonthly[$thisKey]->total ?? 0);
        $lastExpense = (float) ($expenseMonthly[$lastKey]->total ?? 0);

        $incomeGrowth  = $this->growth($thisIncome, $lastIncome);
        $expenseGrowth = $this->growth($thisExpense, $lastExpense);

        $savingRate = $thisIncome > 0
            ? (($thisIncome - $thisExpense) / $thisIncome) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | CATEGORY BREAKDOWN
        |--------------------------------------------------------------------------
        */

        $categories = Expense::query()
            ->selectRaw('category, SUM(amount) as total')
            ->where('family_id', $familyId)
            ->where('is_personal', false)
            ->groupBy('category')
            ->pluck('total', 'category')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | RECENT (STRICT FILTER)
        |--------------------------------------------------------------------------
        */

        $recentIncomes = Income::query()
            ->where('family_id', $familyId)
            ->where('is_personal', false)
            ->latest('income_date')
            ->limit(5)
            ->get();

        $recentExpenses = Expense::query()
            ->where('family_id', $familyId)
            ->where('is_personal', false)
            ->latest('expense_date')
            ->limit(5)
            ->get();

        return [
            'family' => $family,

            'metrics' => [
                'total_income'   => $totalIncome,
                'total_expense'  => $totalExpense,
                'balance'        => $balance,
                'income_growth'  => round($incomeGrowth, 2),
                'expense_growth' => round($expenseGrowth, 2),
                'saving_rate'    => round($savingRate, 2),
            ],

            'trend' => [
                'months'  => $months,
                'income'  => $incomeTrend,
                'expense' => $expenseTrend,
            ],

            'categories' => $categories,

            'recentIncomes'  => $recentIncomes,
            'recentExpenses' => $recentExpenses,
        ];
    }

    private function growth(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }
}