<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Log;
use Throwable;

class FinancialStabilityService
{
    /**
     * Executes the AI Neural Heuristic to determine financial health.
     *
     * @param int $userId
     * @return array
     */
    public function calculate(int $userId): array
    {
        try {
            $year = now()->year;
            $currentMonth = max(1, now()->month); // Prevent division by zero early in the year

            /* ================= 1. MACRO CAPITAL FLOWS ================= */
            $totalIncome = (float) Income::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereYear('income_date', $year)
                ->sum('amount');

            $totalExpense = (float) Expense::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereYear('expense_date', $year)
                ->sum('amount');

            /* ================= 2. RETENTION RATIOS ================= */
            $netSavings = max($totalIncome - $totalExpense, 0);

            $savingsRate = $totalIncome > 0
                ? ($netSavings / $totalIncome) * 100
                : 0.0;

            $expenseRatio = $totalIncome > 0
                ? ($totalExpense / $totalIncome) * 100
                : 100.0; // If no income, you are burning 100% of capital

            /* ================= 3. INCOME VOLATILITY ENGINE (CV MATH) ================= */
            // Single optimized query instead of 12 separate loops
            $monthlyIncomes = Income::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereYear('income_date', $year)
                ->selectRaw('MONTH(income_date) as month, SUM(amount) as total')
                ->groupByRaw('MONTH(income_date)')
                ->pluck('total')
                ->toArray();

            $consistencyScore = 100.0;

            // We need at least 2 months of data to calculate statistical volatility
            if (count($monthlyIncomes) > 1) {
                $mean = array_sum($monthlyIncomes) / count($monthlyIncomes);
                
                if ($mean > 0) {
                    // 1. Calculate standard Variance
                    $variance = array_sum(array_map(function ($x) use ($mean) {
                        return pow($x - $mean, 2);
                    }, $monthlyIncomes)) / count($monthlyIncomes);
                    
                    // 2. Standard Deviation
                    $stdDev = sqrt($variance);
                    
                    // 3. Coefficient of Variation (Standardizes volatility regardless of currency scale)
                    $cv = $stdDev / $mean; 
                    
                    // 0 CV = perfectly consistent (100 score)
                    // > 0.5 CV = highly volatile (0 score)
                    $consistencyScore = max(0, 100 - ($cv * 200)); 
                }
            }

            /* ================= 4. EMERGENCY RUNWAY PROJECTION ================= */
            // Fixes the avg() bug. True monthly burn rate up to the current month.
            $trueMonthlyBurnRate = $totalExpense / $currentMonth;

            // If burn rate is 0 but they have savings, assume infinite (capped) runway
            if ($trueMonthlyBurnRate <= 0) {
                $runwayMonths = $netSavings > 0 ? 12.0 : 0.0;
            } else {
                $runwayMonths = $netSavings / $trueMonthlyBurnRate;
            }

            // Target is 6 months of runway for a perfect score
            $emergencyScore = min(100, ($runwayMonths / 6) * 100);

            /* ================= 5. FINAL NEURAL WEIGHTING ================= */
            // The algorithm prioritizes Savings Rate (40%) and Expense Control (30%)
            $score = 
                ($savingsRate * 0.40) +
                ((100 - min(100, $expenseRatio)) * 0.30) +
                ($consistencyScore * 0.20) +
                ($emergencyScore * 0.10);

            // Cap absolute boundaries
            $finalScore = round(max(min($score, 100), 0), 1);

            return [
                'score'             => $finalScore,
                'savings_rate'      => round($savingsRate, 1),
                'expense_ratio'     => round($expenseRatio, 1),
                'consistency_score' => round($consistencyScore, 1),
                'emergency_score'   => round($emergencyScore, 1),
                'runway'            => round($runwayMonths, 1),
                'riskLevel'         => $this->classifyRisk($finalScore),
            ];

        } catch (Throwable $e) {
            // Graceful degradation on database failure
            Log::error('Financial Stability Engine Failure: ' . $e->getMessage(), ['user_id' => $userId]);

            return [
                'score'             => 0.0,
                'savings_rate'      => 0.0,
                'expense_ratio'     => 100.0,
                'consistency_score' => 0.0,
                'emergency_score'   => 0.0,
                'runway'            => 0.0,
                'riskLevel'         => 'Critical',
            ];
        }
    }

    /**
     * Determines the qualitative risk category based on the numerical score.
     */
    private function classifyRisk(float $score): string
    {
        if ($score >= 80) return 'Optimal';
        if ($score >= 50) return 'Stable';
        if ($score >= 20) return 'Warning';
        return 'Critical';
    }
}