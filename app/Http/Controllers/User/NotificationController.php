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

        /* ================= 5. INSIGHTS FROM THE USER'S OWN RECORDS ================= */
        // Each insight is calculated now from real data. They are not stored events, so they carry no fake timestamps.
        $inr = fn ($v) => '₹' . number_format(abs((float) $v), 0);
        $insights = collect();
        $add = function (int $id, string $tone, string $title, string $message, ?string $actionLabel = null, ?string $actionUrl = null) use (&$insights, $readAlerts) {
            $insights->push((object) [
                'id' => $id, 'tone' => $tone, 'title' => $title, 'message' => $message,
                'action_label' => $actionLabel, 'action_url' => $actionUrl,
                'is_read' => in_array($id, $readAlerts, true),
            ]);
        };

        if ($totalIncome <= 0) {
            $add(self::EVENT_NO_INCOME, 'info', 'No income recorded yet',
                'Add your income so FinanceAI can work out your savings rate and health score.', 'Add income', route('user.incomes.create'));
        }
        if ($balance < 0) {
            $add(self::EVENT_NEG_BALANCE, 'danger', 'You’ve spent more than you’ve earned',
                'Your expenses (' . $inr($totalExpense) . ') are higher than your income (' . $inr($totalIncome) . ') across all your records.', 'See expenses', route('user.expenses.index'));
        }
        if ($thisMonthIncome > 0) {
            $ratio = $thisMonthExpense / $thisMonthIncome * 100;
            if ($ratio > 80) {
                $add(self::EVENT_HIGH_BURN, 'danger', 'Most of this month’s income is spent',
                    'You’ve spent ' . round($ratio) . '% of this month’s income (' . $inr($thisMonthExpense) . ' of ' . $inr($thisMonthIncome) . ').', 'See expenses', route('user.expenses.index'));
            }
        }
        if ($savingRate > 0 && $savingRate < 10) {
            $add(self::EVENT_LOW_SAVINGS, 'warning', 'Saving less than 10% this month',
                'You’ve kept ' . round($savingRate, 1) . '% of this month’s income so far.', 'Open reports', route('user.reports.index', ['range' => 'this_month']));
        }
        if ($lastMonthIncome > 0 && $incomeGrowth < -20) {
            $add(self::EVENT_INCOME_DROP, 'warning', 'Income is down on last month',
                'This month: ' . $inr($thisMonthIncome) . ' · last month: ' . $inr($lastMonthIncome) . ' (' . round($incomeGrowth) . '%).', 'Compare months', route('user.reports.index', ['range' => '3m']));
        }
        if ($savingRate >= 30) {
            $add(self::EVENT_HIGH_SAVINGS, 'success', 'Great month for saving',
                'You’ve kept ' . round($savingRate) . '% of this month’s income — ' . $inr($thisMonthIncome - $thisMonthExpense) . ' so far.');
        }
        if ($thisMonthIncome > 0) {
            $big = Expense::where('user_id', $user->id)
                ->whereBetween($expenseDateCol, [$startOfMonth, $endOfMonth])
                ->where('amount', '>', $thisMonthIncome * 0.25)
                ->orderByDesc('amount')->first();
            if ($big) {
                $add(self::EVENT_WHALE_TX, 'warning', 'One large expense this month',
                    '“' . $big->title . '” (' . $inr($big->amount) . ') is more than a quarter of this month’s income.', 'View expense', route('user.expenses.edit', $big));
            }
        }

        $order = ['danger' => 0, 'warning' => 1, 'info' => 2, 'success' => 3];
        $insights = $insights->sortBy(fn ($i) => [$i->is_read ? 1 : 0, $order[$i->tone] ?? 9])->values();

        /* ================= 6. STORED ALERTS (from the alerts table) ================= */
        $storedAlerts = \App\Models\Alert::where('user_id', $user->id)->latest()->limit(50)->get();
        // Seeing them here counts as reading them: clear the bell count (the list above keeps its "new" dots this once).
        \App\Models\Alert::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);

        return view('user.notifications.index', [
            'insights'         => $insights,
            'storedAlerts'     => $storedAlerts,
            'thisMonthIncome'  => $thisMonthIncome,
            'thisMonthExpense' => $thisMonthExpense,
            'savingRate'       => $savingRate,
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
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
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
            'message' => 'Marked as read.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */
}
