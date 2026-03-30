<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Family;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class FamilyDashboardService
{
    /**
     * Constructs the comprehensive data payload for the Family Hub Dashboard.
     * * @param Family $family
     * @return array
     */
    public function build(Family $family): array
    {
        try {
            return $this->generate($family);
        } catch (Throwable $e) {
            Log::error('Family Dashboard Generation Failed: ' . $e->getMessage(), [
                'family_id' => $family->id
            ]);
            
            return $this->getFallbackState($family);
        }
    }

    /**
     * Core Data Aggregation Engine.
     */
    private function generate(Family $family): array
    {
        $familyId = $family->id;
        
        // Base Scopes for Shared Ledger
        $incomeBase = Income::query()->where('family_id', $familyId)->where('is_personal', false);
        $expenseBase = Expense::query()->where('family_id', $familyId)->where('is_personal', false);

        /*
        |--------------------------------------------------------------------------
        | STRICT TOTALS (LIFETIME)
        |--------------------------------------------------------------------------
        */
        $totalIncome  = (float) (clone $incomeBase)->sum('amount');
        $totalExpense = (float) (clone $expenseBase)->sum('amount');
        $balance      = max($totalIncome - $totalExpense, 0.0);

        /*
        |--------------------------------------------------------------------------
        | TRAILING 6-MONTH TREND (OPTIMIZED SQL)
        |--------------------------------------------------------------------------
        */
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

        $incomeMonthly = (clone $incomeBase)
            ->selectRaw('YEAR(income_date) as yr, MONTH(income_date) as mo, SUM(amount) as total')
            ->where('income_date', '>=', $sixMonthsAgo)
            ->groupByRaw('YEAR(income_date), MONTH(income_date)')
            ->get()
            ->keyBy(fn ($r) => sprintf('%04d-%02d', $r->yr, $r->mo));

        $expenseMonthly = (clone $expenseBase)
            ->selectRaw('YEAR(expense_date) as yr, MONTH(expense_date) as mo, SUM(amount) as total')
            ->where('expense_date', '>=', $sixMonthsAgo)
            ->groupByRaw('YEAR(expense_date), MONTH(expense_date)')
            ->get()
            ->keyBy(fn ($r) => sprintf('%04d-%02d', $r->yr, $r->mo));

        $months       = [];
        $incomeTrend  = [];
        $expenseTrend = [];

        // Build Zero-Filled Time Series Data
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key  = $date->format('Y-m');

            $months[]       = $date->format('M');
            $incomeTrend[]  = (float) ($incomeMonthly[$key]->total ?? 0.0);
            $expenseTrend[] = (float) ($expenseMonthly[$key]->total ?? 0.0);
        }

        /*
        |--------------------------------------------------------------------------
        | MONTH-OVER-MONTH (MoM) VELOCITY
        |--------------------------------------------------------------------------
        */
        $thisKey = now()->format('Y-m');
        $lastKey = now()->subMonth()->format('Y-m');

        $thisIncome  = (float) ($incomeMonthly[$thisKey]->total ?? 0.0);
        $lastIncome  = (float) ($incomeMonthly[$lastKey]->total ?? 0.0);

        $thisExpense = (float) ($expenseMonthly[$thisKey]->total ?? 0.0);
        $lastExpense = (float) ($expenseMonthly[$lastKey]->total ?? 0.0);

        $incomeGrowth  = $this->calculateGrowth($thisIncome, $lastIncome);
        $expenseGrowth = $this->calculateGrowth($thisExpense, $lastExpense);

        // Calculate this specific month's savings rate
        $savingRate = $thisIncome > 0
            ? (($thisIncome - $thisExpense) / $thisIncome) * 100
            : 0.0;

        /*
        |--------------------------------------------------------------------------
        | CATEGORY BREAKDOWN (CURRENT YEAR)
        |--------------------------------------------------------------------------
        */
        $categories = (clone $expenseBase)
            ->selectRaw('category, SUM(amount) as total')
            ->whereYear('expense_date', now()->year)
            ->groupBy('category')
            ->orderByDesc('total')
            ->pluck('total', 'category')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | RECENT TELEMETRY (LEDGER FEED)
        |--------------------------------------------------------------------------
        */
        $recentIncomes = (clone $incomeBase)
            ->latest('income_date')
            ->latest('id')
            ->limit(5)
            ->get();

        $recentExpenses = (clone $expenseBase)
            ->latest('expense_date')
            ->latest('id')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | COMPILE SECURE PAYLOAD
        |--------------------------------------------------------------------------
        */
        return [
            'family' => $family,

            'metrics' => [
                'total_income'   => $totalIncome,
                'total_expense'  => $totalExpense,
                'balance'        => $balance,
                'income_growth'  => round($incomeGrowth, 1),
                'expense_growth' => round($expenseGrowth, 1),
                'saving_rate'    => round(max($savingRate, 0), 1),
            ],

            'trend' => [
                'months'  => $months,
                'income'  => $incomeTrend,
                'expense' => $expenseTrend,
            ],

            'categories'     => $categories,
            'recentIncomes'  => $recentIncomes,
            'recentExpenses' => $recentExpenses,
        ];
    }

    /**
     * Calculates the percentage change between two financial periods.
     */
    private function calculateGrowth(float $current, float $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Graceful degradation state if the database query fails.
     */
    private function getFallbackState(Family $family): array
    {
        return [
            'family'         => $family,
            'metrics'        => [
                'total_income'   => 0.0,
                'total_expense'  => 0.0,
                'balance'        => 0.0,
                'income_growth'  => 0.0,
                'expense_growth' => 0.0,
                'saving_rate'    => 0.0,
            ],
            'trend'          => [
                'months'  => ['M1', 'M2', 'M3', 'M4', 'M5', 'M6'],
                'income'  => [0, 0, 0, 0, 0, 0],
                'expense' => [0, 0, 0, 0, 0, 0],
            ],
            'categories'     => [],
            'recentIncomes'  => collect(),
            'recentExpenses' => collect(),
            'systemError'    => true, // Exposes error state to the frontend
        ];
    }
}