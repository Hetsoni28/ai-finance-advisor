<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportService
{
    /**
     * Cache for resolved column names to prevent N+1 Schema queries.
     */
    private ?string $incomeDateColumn = null;
    private ?string $expenseDateColumn = null;

    /*
    |--------------------------------------------------------------------------
    | MAIN FINANCIAL SUMMARY (REAL-TIME AGGREGATION ENGINE)
    |--------------------------------------------------------------------------
    */
    public function summary(User $user, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): array
    {
        // 1. Establish Base Queries with Strict Tenant Isolation
        $incomeQuery  = Income::query()->where('user_id', $user->id);
        $expenseQuery = Expense::query()->where('user_id', $user->id);

        // Safeguard: Only fetch personal ledger data (ignore family data for this report)
        if ($this->hasColumn('incomes', 'is_personal')) {
            $incomeQuery->where('is_personal', true);
        }
        if ($this->hasColumn('expenses', 'is_personal')) {
            $expenseQuery->where('is_personal', true);
        }

        // 2. Resolve accurate date columns safely
        $incCol = $this->getIncomeDateColumn();
        $expCol = $this->getExpenseDateColumn();

        // 3. Calculate Global KPI Totals (Scoped to Requested Date Range)
        $kpiIncomeQuery = clone $incomeQuery;
        $kpiExpenseQuery = clone $expenseQuery;

        if ($from && $to) {
            $kpiIncomeQuery->whereBetween($incCol, [$from, $to]);
            $kpiExpenseQuery->whereBetween($expCol, [$from, $to]);
        }

        $totalIncome  = (float) $kpiIncomeQuery->sum('amount');
        $totalExpense = (float) $kpiExpenseQuery->sum('amount');

        // 4. Generate 6-Month Trailing Velocity Matrix (For the Trend Chart)
        // We calculate this dynamically so the UI charts are always perfectly accurate.
        $trendLabels  = collect();
        $trendIncome  = collect();
        $trendExpense = collect();

        for ($i = 5; $i >= 0; $i--) {
            // Get boundaries for the targeted trailing month
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd   = now()->subMonths($i)->endOfMonth();

            $trendLabels->push($monthStart->format('M')); // e.g., "Oct", "Nov"

            // Query sums for that specific month slice
            $monthInc = (clone $incomeQuery)->whereBetween($incCol, [$monthStart, $monthEnd])->sum('amount');
            $monthExp = (clone $expenseQuery)->whereBetween($expCol, [$monthStart, $monthEnd])->sum('amount');

            $trendIncome->push((float) $monthInc);
            $trendExpense->push((float) $monthExp);
        }

        // 5. Return Unified Enterprise Payload
        return [
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance'      => $totalIncome - $totalExpense,
            
            // Injected Chart Data specifically required by the Blade View
            'trendLabels'  => $trendLabels,
            'trendIncome'  => $trendIncome,
            'trendExpense' => $trendExpense,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY BREAKDOWN (DIVERSIFICATION MATRIX)
    |--------------------------------------------------------------------------
    */
    public function categoryBreakdown(User $user, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null)
    {
        $query = Expense::select(
            'category',
            DB::raw('SUM(amount) as total')
        )->where('user_id', $user->id);

        if ($this->hasColumn('expenses', 'is_personal')) {
            $query->where('is_personal', true);
        }

        if ($from && $to) {
            $query->whereBetween($this->getExpenseDateColumn(), [$from, $to]);
        }

        return $query
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                // Ensure strict float casting for UI display
                $row->total = (float) $row->total;
                return $row;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | INTERNAL HELPERS (MEMORY & PERFORMANCE OPTIMIZATION)
    |--------------------------------------------------------------------------
    */

    private function getIncomeDateColumn(): string
    {
        if ($this->incomeDateColumn === null) {
            $this->incomeDateColumn = $this->hasColumn('incomes', 'income_date') ? 'income_date' : 'created_at';
        }
        return $this->incomeDateColumn;
    }

    private function getExpenseDateColumn(): string
    {
        if ($this->expenseDateColumn === null) {
            $this->expenseDateColumn = $this->hasColumn('expenses', 'expense_date') ? 'expense_date' : 'created_at';
        }
        return $this->expenseDateColumn;
    }

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }
}