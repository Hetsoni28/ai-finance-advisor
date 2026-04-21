<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class FinancialMetricsService
{
    /**
     * @var array Request-lifecycle cache to prevent N+1 query cascades.
     */
    private array $cache = [];

    /*
    |--------------------------------------------------------------------------
    | TOTAL INCOME
    |--------------------------------------------------------------------------
    */
    public function totalIncome(int $userId): float
    {
        $cacheKey = "income_{$userId}";

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = (float) Income::where('user_id', $userId)
                ->where('is_personal', true)
                ->sum('amount');
        }

        return $this->cache[$cacheKey];
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL EXPENSE
    |--------------------------------------------------------------------------
    */
    public function totalExpense(int $userId): float
    {
        $cacheKey = "expense_{$userId}";

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = (float) Expense::where('user_id', $userId)
                ->where('is_personal', true)
                ->sum('amount');
        }

        return $this->cache[$cacheKey];
    }

    /*
    |--------------------------------------------------------------------------
    | NET SAVINGS
    |--------------------------------------------------------------------------
    */
    public function netSavings(int $userId): float
    {
        // Relies on memoized methods. 0 extra DB queries.
        return max($this->totalIncome($userId) - $this->totalExpense($userId), 0.0);
    }

    /*
    |--------------------------------------------------------------------------
    | SAVINGS RATE (%)
    |--------------------------------------------------------------------------
    */
    public function savingsRate(int $userId): float
    {
        $income = $this->totalIncome($userId);

        if ($income <= 0) {
            return 0.0;
        }

        // Relies on memoized methods. 0 extra DB queries.
        return round(($this->netSavings($userId) / $income) * 100, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | CURRENT MONTH TREND (FIXED: Uses actual transaction dates)
    |--------------------------------------------------------------------------
    */
    public function currentMonthSummary(int $userId): array
    {
        try {
            $start = now()->startOfMonth()->toDateString();
            $end   = now()->endOfMonth()->toDateString();

            $income = (float) Income::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereBetween('income_date', [$start, $end])
                ->sum('amount');

            $expense = (float) Expense::where('user_id', $userId)
                ->where('is_personal', true)
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            return [
                'income'  => $income,
                'expense' => $expense,
                'net'     => $income - $expense,
            ];
            
        } catch (Throwable $e) {
            Log::error('Metrics Calculation Failed: ' . $e->getMessage(), ['user_id' => $userId]);
            
            // Graceful degradation
            return [
                'income'  => 0.0,
                'expense' => 0.0,
                'net'     => 0.0,
            ];
        }
    }
    
    /*
    |--------------------------------------------------------------------------
    | FLUSH CACHE (For long-running workers/daemons)
    |--------------------------------------------------------------------------
    */
    public function flushCache(): void
    {
        $this->cache = [];
    }
}