<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * 🚀 FinanceAI Neural Heuristic Engine
 * Processes macro-financial telemetry into actionable stability intelligence.
 */
final class FinancialAIService
{
    /**
     * Internal request cache to prevent redundant SQL execution.
     */
    private static array $requestCache = [];

    public function generateDashboardData(int $userId): array
    {
        if (isset(self::$requestCache[$userId])) {
            return self::$requestCache[$userId];
        }

        try {
            $currentYear = Carbon::now()->year;

            /* =====================================================
             | 1. TOTALS (LIFETIME PERSONAL AGGREGATES)
             ====================================================== */
            $income = (float) Income::where('user_id', $userId)->where('is_personal', true)->sum('amount');
            $expense = (float) Expense::where('user_id', $userId)->where('is_personal', true)->sum('amount');

            $savings = max(0, $income - $expense);
            $savingRate = $income > 0 ? round(($savings / $income) * 100, 2) : 0.0;

            /* =====================================================
             | 2. TIME-SERIES DATA (ZERO-FILLED AGGREGATION)
             ====================================================== */
            // Optimized query using actual transaction dates, not created_at
            $incomeRows = Income::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereYear('income_date', $currentYear)
                ->selectRaw("DATE_FORMAT(income_date, '%Y-%m') as month_key, SUM(amount) as total")
                ->groupByRaw("month_key")
                ->orderBy('month_key')
                ->pluck('total', 'month_key')
                ->toArray();

            $expenseRows = Expense::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereYear('expense_date', $currentYear)
                ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month_key, SUM(amount) as total")
                ->groupByRaw("month_key")
                ->orderBy('month_key')
                ->pluck('total', 'month_key')
                ->toArray();

            // Create a continuous 12-month map to prevent array jaggedness
            $labels = [];
            $incomeSeries = [];
            $expenseSeries = [];
            $netWorthSeries = [];
            $cumulative = 0.0;

            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::create($currentYear, $m, 1);
                $key = $date->format('Y-m');

                $inc = (float) ($incomeRows[$key] ?? 0.0);
                $exp = (float) ($expenseRows[$key] ?? 0.0);
                
                $cumulative += ($inc - $exp);

                $labels[] = $date->format('M Y');
                $incomeSeries[] = $inc;
                $expenseSeries[] = $exp;
                $netWorthSeries[] = $cumulative;
            }

            /* =====================================================
             | 3. CATEGORICAL TELEMETRY
             ====================================================== */
            $categoryData = Expense::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereYear('expense_date', $currentYear)
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->get();

            /* =====================================================
             | 4. STABILITY HEURISTICS (THE BRAIN)
             ====================================================== */
            $activeMonths = max(count(array_filter($expenseSeries)), 1);
            $avgMonthlyExpense = array_sum($expenseSeries) / $activeMonths;
            
            // Runway Calculation (Survival Duration)
            $runway = $avgMonthlyExpense > 0 ? round($savings / $avgMonthlyExpense, 1) : 12.0;

            // Income Volatility Logic (Consistency Score)
            $incomeVariancePenality = 0;
            $filteredIncomes = array_filter($incomeSeries);
            if(count($filteredIncomes) > 1) {
                $mean = array_sum($filteredIncomes) / count($filteredIncomes);
                $sqDiffs = array_map(fn($v) => pow($v - $mean, 2), $filteredIncomes);
                $stdDev = sqrt(array_sum($sqDiffs) / count($filteredIncomes));
                $cv = $stdDev / ($mean ?: 1); // Coefficient of Variation
                $incomeVariancePenality = $cv * 15; // Max 15 point penalty for high volatility
            }

            // Balanced Scoring Algorithm
            $scoreBase = ($savingRate * 0.8) + (min($runway, 12) * 3);
            $finalScore = (int) min(100, max(0, round($scoreBase - $incomeVariancePenality)));

            $risk = match (true) {
                $finalScore >= 90 => 'Pristine Tier',
                $finalScore >= 75 => 'Stable Node',
                $finalScore >= 55 => 'Nominal',
                $finalScore >= 35 => 'Moderate Risk',
                default          => 'High Burn Alert',
            };

            $payload = [
                'totalIncome'     => $income,
                'totalExpense'    => $expense,
                'savings'         => $savings,
                'savingRate'      => $savingRate,
                'score'           => $finalScore,
                'riskLevel'       => $risk,
                'runway'          => $runway,
                'labels'          => $labels,
                'incomeSeries'    => $incomeSeries,
                'expenseSeries'   => $expenseSeries,
                'netWorthSeries'  => $netWorthSeries,
                'categoryLabels'  => $categoryData->pluck('category')->toArray(),
                'categorySeries'  => $categoryData->pluck('total')->map(fn($v) => (float)$v)->toArray(),
                'isStale'         => false
            ];

            self::$requestCache[$userId] = $payload;
            return $payload;

        } catch (Throwable $e) {
            Log::error('Neural Engine Failure: ' . $e->getMessage(), ['user' => $userId]);
            return $this->getFallbackState();
        }
    }

    /**
     * Prevents UI crash if database is under maintenance.
     */
    private function getFallbackState(): array
    {
        return [
            'totalIncome' => 0.0, 'totalExpense' => 0.0, 'savings' => 0.0, 'savingRate' => 0.0,
            'score' => 0, 'riskLevel' => 'Offline', 'runway' => 0.0,
            'labels' => [], 'incomeSeries' => [], 'expenseSeries' => [], 'netWorthSeries' => [],
            'categoryLabels' => [], 'categorySeries' => [], 'isStale' => true
        ];
    }
}