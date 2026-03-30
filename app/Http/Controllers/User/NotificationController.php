<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class NotificationController extends Controller
{
    /**
     * 🛡️ DETERMINISTIC EVENT REGISTRY
     * Maps permanent, unique IDs to specific system anomalies.
     * This prevents state desync when refreshing the page.
     */
    private const EVENT_NO_INCOME    = 1001;
    private const EVENT_NEG_BALANCE  = 1002;
    private const EVENT_LOW_SAVINGS  = 1003;
    private const EVENT_HIGH_BURN    = 1004;
    private const EVENT_INCOME_DROP  = 1005;
    private const EVENT_HIGH_SAVINGS = 1006;
    private const EVENT_WHALE_TX     = 1007; // 🐳 New Beast Mode Anomaly

    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATION DASHBOARD (AI HEURISTIC ENGINE)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user instanceof User, 403, 'Unauthorized Node Access');

        $now = now();

        /* ================= 1. SAFE DATE RANGES ================= */
        $startOfMonth   = $now->copy()->startOfMonth();
        $endOfMonth     = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd   = $now->copy()->subMonth()->endOfMonth();

        /* ================= 2. COLUMN SAFETY GUARD ================= */
        $incomeDateCol  = Schema::hasColumn('incomes', 'income_date') ? 'income_date' : 'created_at';
        $expenseDateCol = Schema::hasColumn('expenses', 'expense_date') ? 'expense_date' : 'created_at';

        /* ================= 3. CORE METRICS ENGINE ================= */
        $totalIncome  = (float) Income::where('user_id', $user->id)->sum('amount');
        $totalExpense = (float) Expense::where('user_id', $user->id)->sum('amount');

        $thisMonthIncome = (float) Income::where('user_id', $user->id)
            ->whereBetween($incomeDateCol, [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $thisMonthExpense = (float) Expense::where('user_id', $user->id)
            ->whereBetween($expenseDateCol, [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $lastMonthIncome = (float) Income::where('user_id', $user->id)
            ->whereBetween($incomeDateCol, [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        /* ================= 4. SMART ANALYTICS LOGIC ================= */
        $savingRate = $thisMonthIncome > 0
            ? (($thisMonthIncome - $thisMonthExpense) / $thisMonthIncome) * 100
            : 0;

        $incomeGrowth = $lastMonthIncome > 0
            ? (($thisMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100
            : 0;

        // Retrieve "Read" state memory from encrypted session
        $readAlerts = session()->get("user_{$user->id}_read_alerts", []);
        $notifications = collect();

        /* ================= 5. VIRTUAL ALERT GENERATOR ================= */
        
        if ($totalIncome <= 0) {
            $notifications->push($this->makeAlert(self::EVENT_NO_INCOME, 'info', 'No inbound capital recorded yet. Start tracking your income to unlock AI analytics.', 'low', $readAlerts, 45));
        }

        if ($balance < 0) {
            $notifications->push($this->makeAlert(self::EVENT_NEG_BALANCE, 'danger', 'Critical Deficit: Your aggregate expenses have exceeded your total recorded income.', 'high', $readAlerts, 5));
        }

        if ($savingRate > 0 && $savingRate < 10) {
            $notifications->push($this->makeAlert(self::EVENT_LOW_SAVINGS, 'warning', 'Low Savings Velocity: Your retention rate is below 10% for the current operational month.', 'medium', $readAlerts, 12));
        }

        if ($thisMonthIncome > 0) {
            $ratio = ($thisMonthExpense / $thisMonthIncome) * 100;
            if ($ratio > 80) {
                $notifications->push($this->makeAlert(self::EVENT_HIGH_BURN, 'danger', "High Burn Rate: You have exhausted over " . round($ratio) . "% of your current month's liquidity.", 'high', $readAlerts, 2));
            }
        }

        if ($incomeGrowth < -20) {
            $notifications->push($this->makeAlert(self::EVENT_INCOME_DROP, 'warning', 'Revenue Contraction: Your inbound capital has dropped by more than 20% compared to the previous fiscal month.', 'medium', $readAlerts, 24));
        }

        if ($savingRate >= 30) {
            $notifications->push($this->makeAlert(self::EVENT_HIGH_SAVINGS, 'success', 'Optimal Performance: You are retaining over 30% of your operational income. Excellent capital management.', 'low', $readAlerts, 36));
        }

        /* 🔥 BEAST MODE: WHALE TRANSACTION DETECTOR */
        if ($thisMonthIncome > 0) {
            $whaleThreshold = $thisMonthIncome * 0.25; // 25% of monthly income
            $whaleTx = Expense::where('user_id', $user->id)
                ->whereBetween($expenseDateCol, [$startOfMonth, $endOfMonth])
                ->where('amount', '>', $whaleThreshold)
                ->orderByDesc('amount')
                ->first();

            if ($whaleTx) {
                $msg = "Capital Hemorrhage Detected: A massive transaction ('{$whaleTx->category}') recently consumed over 25% of your monthly inbound capital.";
                $notifications->push($this->makeAlert(self::EVENT_WHALE_TX, 'danger', $msg, 'high', $readAlerts, 1));
            }
        }

        /* ================= 6. DATA NORMALIZATION & PAGINATION ================= */
        
        // Sort latest first (using the simulated created_at time)
        $sortedAlerts = $notifications->sortByDesc('created_at')->values();

        // High-Performance Manual Paginator
        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentItems = $sortedAlerts->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $paginatedAlerts = new LengthAwarePaginator(
            $currentItems, 
            $sortedAlerts->count(), 
            $perPage, 
            $currentPage, 
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('user.notifications.index', [
            'notifications' => $paginatedAlerts,
            'totalIncome'   => $totalIncome,
            'totalExpense'  => $totalExpense,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATE MANAGEMENT (MARK AS READ API)
    |--------------------------------------------------------------------------
    */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Node'], 403);
        }

        // Validate that the requested ID belongs to our Deterministic Event Registry
        $validEvents = [
            self::EVENT_NO_INCOME, self::EVENT_NEG_BALANCE, self::EVENT_LOW_SAVINGS,
            self::EVENT_HIGH_BURN, self::EVENT_INCOME_DROP, self::EVENT_HIGH_SAVINGS,
            self::EVENT_WHALE_TX
        ];

        if (in_array($id, $validEvents, true)) {
            $sessionKey = "user_{$user->id}_read_alerts";
            $readAlerts = session()->get($sessionKey, []);
            
            // Append to session array if it doesn't exist
            if (!in_array($id, $readAlerts, true)) {
                $readAlerts[] = $id;
                session()->put($sessionKey, $readAlerts);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Alert state successfully synchronized.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Virtual Alert Factory
     * Assembles standardized alert objects with simulated timing.
     */
    private function makeAlert(int $id, string $type, string $message, string $priority, array $readAlerts, int $minutesAgo): object
    {
        return (object) [
            'id'         => $id,
            'type'       => $type,
            'message'    => $message,
            'priority'   => $priority,
            'is_read'    => in_array($id, $readAlerts, true),
            'created_at' => now()->subMinutes($minutesAgo)->format('Y-m-d H:i:s'),
        ];
    }
}