<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Alert;
use App\Services\FinancialStabilityService;
use Carbon\Carbon;
use Throwable;

class ChartController extends Controller
{
    /**
     * ========================================================================
     * 🖥️ MASTER DASHBOARD VIEW
     * ========================================================================
     */
    public function dashboard(FinancialStabilityService $stability): View
    {
        $userId = Auth::id();
        abort_unless($userId, 403, 'Cryptographic handshake failed.');

        try {
            $data = $this->getDashboardData($userId);

            // 🔥 Inject heuristic score engine safely
            $scoreData = $stability->calculate($userId);
            $finalScore = (float) ($scoreData['score'] ?? 0);

            return view('dashboard.index', array_merge($data, [
                'financialHealthScore' => $finalScore,
                'riskLevel'            => $this->classifyRisk($finalScore),
            ]));

        } catch (Throwable $e) {
            Log::error('Dashboard Rendering Failure: ' . $e->getMessage(), ['user_id' => $userId]);
            
            // Fallback safe-state to prevent UI white-screens
            return view('dashboard.index', array_merge($this->getEmptyDashboardState(), [
                'financialHealthScore' => 0,
                'riskLevel'            => 'Unknown',
                'systemError'          => true
            ]));
        }
    }

    /**
     * ========================================================================
     * 📡 LIVE TELEMETRY API (AJAX)
     * ========================================================================
     */
    public function live(): JsonResponse
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized session.'], 401);
        }

        try {
            return response()->json($this->getDashboardData($userId));
        } catch (Throwable $e) {
            Log::error('Live Telemetry API Failure: ' . $e->getMessage(), ['user_id' => $userId]);
            return response()->json(['error' => 'Data synchronization failed.'], 500);
        }
    }

    /**
     * ========================================================================
     * 🧠 CORE DATA AGGREGATION ENGINE (OPTIMIZED)
     * ========================================================================
     */
    private function getDashboardData(int $userId): array
    {
        $year = now()->year;

        // 1. Establish Base Queries (DRY Principle)
        $baseIncomeQuery = Income::where('user_id', $userId)->where('is_personal', true);
        $baseExpenseQuery = Expense::where('user_id', $userId)->where('is_personal', true);

        /* |--------------------------------------------------------------------------
        | MACRO TOTALS
        |--------------------------------------------------------------------------
        */
        $totalIncome = (float) (clone $baseIncomeQuery)->sum('amount');
        $totalExpense = (float) (clone $baseExpenseQuery)->sum('amount');

        $savings = max($totalIncome - $totalExpense, 0);
        $savingRate = $totalIncome > 0 ? round(($savings / $totalIncome) * 100, 1) : 0.0;

        /* |--------------------------------------------------------------------------
        | RECENT LEDGER ENTRIES
        |--------------------------------------------------------------------------
        */
        $recentIncomes = (clone $baseIncomeQuery)
            ->latest('income_date')
            ->limit(5)
            ->get();

        $recentExpenses = (clone $baseExpenseQuery)
            ->latest('expense_date')
            ->limit(5)
            ->get();

        /* |--------------------------------------------------------------------------
        | SYSTEM ALERTS
        |--------------------------------------------------------------------------
        */
        $alerts = Alert::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        /* |--------------------------------------------------------------------------
        | 🔥 BEAST MODE: SINGLE-QUERY MONTHLY AGGREGATION
        | Prevents the N+1 Loop of Death. Fetches all 12 months in ~2ms.
        |--------------------------------------------------------------------------
        */
        // Map raw SQL aggregation to an associative array: [1 => 4500, 2 => 3200, ...]
        $monthlyRawData = (clone $baseExpenseQuery)
            ->whereYear('expense_date', $year)
            ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->groupByRaw('MONTH(expense_date)')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyExpenseLabels = [];
        $monthlyExpenseTotals = [];

        // Zero-Fill Algorithm: Ensures Chart.js gets exactly 12 nodes, even if months are missing
        for ($month = 1; $month <= 12; $month++) {
            $monthlyExpenseLabels[] = Carbon::create($year, $month, 1)->format('M');
            $monthlyExpenseTotals[] = isset($monthlyRawData[$month]) ? (float) $monthlyRawData[$month] : 0.0;
        }

        /* |--------------------------------------------------------------------------
        | CATEGORICAL DISTRIBUTION
        |--------------------------------------------------------------------------
        */
        $categoryData = (clone $baseExpenseQuery)
            ->whereYear('expense_date', $year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $categoryExpenseLabels = $categoryData->pluck('category')->values()->toArray();
        $categoryExpenseTotals = $categoryData->pluck('total')->map(fn ($val) => (float) $val)->values()->toArray();

        /* |--------------------------------------------------------------------------
        | RETURN PAYLOAD
        |--------------------------------------------------------------------------
        */
        return [
            'totalIncome'           => $totalIncome,
            'totalExpense'          => $totalExpense,
            'savings'               => $savings,
            'savingRate'            => $savingRate,
            'recentIncomes'         => $recentIncomes,
            'recentExpenses'        => $recentExpenses,
            'alerts'                => $alerts,
            'monthlyExpenseLabels'  => $monthlyExpenseLabels,
            'monthlyExpenseTotals'  => $monthlyExpenseTotals,
            'categoryExpenseLabels' => $categoryExpenseLabels,
            'categoryExpenseTotals' => $categoryExpenseTotals,
        ];
    }

    /**
     * ========================================================================
     * 🛡️ SAFE FALLBACK STATE
     * Returns empty parameters if the database connection drops.
     * ========================================================================
     */
    private function getEmptyDashboardState(): array
    {
        return [
            'totalIncome'           => 0.0,
            'totalExpense'          => 0.0,
            'savings'               => 0.0,
            'savingRate'            => 0.0,
            'recentIncomes'         => collect(),
            'recentExpenses'        => collect(),
            'alerts'                => collect(),
            'monthlyExpenseLabels'  => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'monthlyExpenseTotals'  => array_fill(0, 12, 0.0),
            'categoryExpenseLabels' => [],
            'categoryExpenseTotals' => [],
        ];
    }

    /**
     * ========================================================================
     * 📊 RISK CLASSIFICATION ENGINE
     * ========================================================================
     */
    private function classifyRisk(float $score): string
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Stable';
        if ($score >= 40) return 'Warning';
        return 'Critical';
    }
}