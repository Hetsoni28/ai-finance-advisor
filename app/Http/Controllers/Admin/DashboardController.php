<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity; // 🚨 FIX: Replaced custom model with standard Spatie logging
use Throwable;

class DashboardController extends Controller
{
    /**
     * Enforce strict Master Node authentication.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /*
    |--------------------------------------------------------------------------
    | MASTER ADMIN DASHBOARD (OPTIMIZED)
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        $now = Carbon::now();

        /* ================= 1. CACHED CORE METRICS ================= */
        // Caching heavy table counts prevents CPU spikes on the admin dashboard
        $metrics = Cache::remember('admin_dashboard_metrics', 60, function () use ($now) {
            $lastMonth = $now->copy()->subMonth();

            return [
                'totalUsers'      => User::count(),
                'blockedUsers'    => User::where('is_blocked', true)->count(),
                'newUsersThisMonth'=> User::where('created_at', '>=', $lastMonth)->count(),
                'totalIncome'     => Income::sum('amount'),
                'totalExpenses'   => Expense::sum('amount'),
            ];
        });

        /* ================= 2. THE 6-MONTH AGGREGATION ENGINE ================= */
        // 🚨 FIX: Replaced the 12-query loop with 2 highly optimized aggregate queries.
        $startDate = $now->copy()->subMonths(5)->startOfMonth();

        // Query raw monthly sums in a single DB hit
        $incomeData = Income::where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(amount) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $expenseData = Expense::where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(amount) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        // Map the results cleanly for Chart.js
        $months = [];
        $monthlyIncome = [];
        $monthlyExpenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $targetMonth = $now->copy()->subMonths($i);
            $monthKey = $targetMonth->format('Y-m'); // e.g., "2023-10"
            
            $months[] = $targetMonth->format('M Y'); // e.g., "Oct 2023"
            $monthlyIncome[] = (float) ($incomeData[$monthKey] ?? 0);
            $monthlyExpenses[] = (float) ($expenseData[$monthKey] ?? 0);
        }

        /* ================= 3. RECENT SYSTEM AUDIT ================= */
        // Fetch global telemetry logs
        $activities = Activity::with('causer')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', [
            // Core Metrics
            'totalUsers'      => $metrics['totalUsers'],
            'blockedUsers'    => $metrics['blockedUsers'],
            'newUsersThisMonth'=> $metrics['newUsersThisMonth'],
            'totalIncome'     => $metrics['totalIncome'],
            'totalExpenses'   => $metrics['totalExpenses'],
            
            // Chart Telemetry
            'months'          => $months,
            'monthlyIncome'   => $monthlyIncome,
            'monthlyExpenses' => $monthlyExpenses,
            
            // Global Audit
            'activities'      => $activities,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | IAM SECURITY: BLOCK / UNBLOCK NODE
    |--------------------------------------------------------------------------
    */
    public function toggleBlock(User $user): RedirectResponse
    {
        // 1. Immutable Rule: Admins cannot kamikaze their own account
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Operation denied: You cannot alter your own access privileges.');
        }

        // 2. Immutable Rule: Prevent locking out the system completely
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count(); // Adjust column name if you use Spatie Roles
            if ($adminCount <= 1) {
                return back()->with('error', 'Operation denied: System requires at least one active Master Node.');
            }
        }

        try {
            // 🚨 FIX: Transaction with deadlock retry (3)
            DB::transaction(function () use ($user) {
                $user->update([
                    'is_blocked' => ! $user->is_blocked,
                ]);

                // 🚨 FIX: Converted to industry-standard Spatie Activity syntax
                $status = $user->is_blocked ? 'Suspended' : 'Restored';
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($user)
                    ->log("{$status} network access for identity node: {$user->email}");

                Cache::forget('admin_dashboard_metrics');
            }, 3);

            $message = $user->is_blocked ? 'User access suspended.' : 'User access restored.';
            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error('Node Suspension Failed', [
                'target_user' => $user->id,
                'admin_user'  => Auth::id(),
                'error'       => $e->getMessage()
            ]);
            return back()->with('error', 'System failure during status update. Review server logs.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | IAM SECURITY: PURGE NODE (DELETE)
    |--------------------------------------------------------------------------
    */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Operation denied: You cannot purge your own active session.');
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Operation denied: Cannot purge the final Master Node.');
            }
        }

        try {
            DB::transaction(function () use ($user) {
                
                // Log the purge *before* the user is deleted so relations exist
                activity()
                    ->causedBy(Auth::user())
                    ->log("Initiated global purge for identity node: {$user->email}");

                // Assuming you have cascading deletes set up in your DB or Model boot methods
                $user->delete();

                Cache::forget('admin_dashboard_metrics');
            }, 3);

            return back()->with('success', 'Identity node purged from the network.');

        } catch (Throwable $e) {
            Log::error('Node Purge Failed', [
                'target_user' => $user->id,
                'admin_user'  => Auth::id(),
                'error'       => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to purge identity. Related cryptographic ledgers may require manual detachment.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL AUDIT LOGS
    |--------------------------------------------------------------------------
    */
    public function activities(): View
    {
        // 🚨 FIX: Eager load 'subject' to prevent polymorphic N+1 queries in the view
        $activities = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate(15);

        return view('admin.activities.index', compact('activities'));
    }
}