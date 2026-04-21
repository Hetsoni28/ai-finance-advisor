<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        // Enforce strict authentication and admin-only access
        $this->middleware(['auth', 'admin']);
    }

    /*
    |--------------------------------------------------------------------------
    | USER LIST & GOVERNANCE DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $query = User::query();

        // 🔎 1. Advanced Search (Name, Email, and Exact ID)
        if ($request->filled('search')) {
            $search = trim($request->search);
            
            // Strip the '#' if an admin searches by ID format (e.g., #0012)
            $cleanSearchId = ltrim($search, '#');

            $query->where(function ($q) use ($search, $cleanSearchId) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', $cleanSearchId);
            });
        }

        // 🚦 2. Status Filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_blocked', false);
            } elseif ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            }
        }

        // 🎭 3. Role Filter (🚨 FIXED: Was missing in original code)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // ⚡ Execute Query with Pagination
        $users = $query->latest('id')
            ->paginate(15)
            ->withQueryString();

        // 📊 Cached KPI Stats (Cache busts automatically on changes)
        $stats = Cache::remember('admin_user_stats', now()->addMinutes(30), function () {
            // Using aggregate counts in a single query for massive performance boost
            $data = User::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_blocked = 0 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_blocked = 1 THEN 1 ELSE 0 END) as blocked,
                SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as admins
            ', [User::ROLE_ADMIN ?? 'admin'])->first();

            return [
                'total'   => $data->total ?? 0,
                'active'  => $data->active ?? 0,
                'blocked' => $data->blocked ?? 0,
                'admins'  => $data->admins ?? 0,
            ];
        });

        return view('admin.users.index', compact('users', 'stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | SINGLE USER: BLOCK / UNBLOCK
    |--------------------------------------------------------------------------
    */
    public function block(User $user): RedirectResponse
    {
        // ❌ Prevent blocking self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Security constraint: You cannot suspend your own account.');
        }

        // ❌ Prevent blocking the final admin
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $adminCount = User::where('role', User::ROLE_ADMIN ?? 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Security constraint: Cannot suspend the last remaining system administrator.');
            }
        }

        try {
            DB::transaction(function () use ($user) {
                // Toggle status
                $user->update([
                    'is_blocked' => !$user->is_blocked,
                ]);

                // Log Activity securely
                Activity::create([
                    'user_id'     => Auth::id(),
                    'description' => $user->is_blocked 
                        ? "Suspended account access for user: {$user->email}"
                        : "Restored account access for user: {$user->email}",
                ]);

                // Bust cache so KPI cards update instantly
                Cache::forget('admin_user_stats');
            });

            $action = $user->is_blocked ? 'suspended' : 'restored';
            return back()->with('success', "User account successfully {$action}.");

        } catch (\Exception $e) {
            Log::error("User Block Error: " . $e->getMessage());
            return back()->with('error', 'A database error occurred while updating the user status.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BULK ACTIONS (🚨 NEW FUN: Handle multi-select from Frontend)
    |--------------------------------------------------------------------------
    */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:block,unblock',
            'user_ids' => 'required|string', // Comma separated IDs from frontend
        ]);

        $userIds = explode(',', $request->user_ids);
        $action = $request->action;
        $authId = Auth::id();

        // Remove currently authenticated user from the target list (cannot self-block)
        $userIds = array_diff($userIds, [$authId]);

        if (empty($userIds)) {
            return back()->with('error', 'No valid users selected for bulk action.');
        }

        try {
            DB::transaction(function () use ($userIds, $action, $authId) {
                
                $isBlockedState = ($action === 'block') ? true : false;

                // Bulk Update
                User::whereIn('id', $userIds)->update([
                    'is_blocked' => $isBlockedState
                ]);

                // Log Bulk Activity
                Activity::create([
                    'user_id'     => $authId,
                    'description' => "Bulk " . ($isBlockedState ? "suspended" : "restored") . " access for " . count($userIds) . " users.",
                ]);

                Cache::forget('admin_user_stats');
            });

            return back()->with('success', "Bulk action applied successfully.");

        } catch (\Exception $e) {
            Log::error("Bulk Action Error: " . $e->getMessage());
            return back()->with('error', 'A critical error occurred processing the bulk action.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SINGLE USER: DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(User $user): RedirectResponse
    {
        // ❌ Prevent deleting self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Security constraint: You cannot delete your own account.');
        }

        // ❌ Prevent deleting the final admin
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $adminCount = User::where('role', User::ROLE_ADMIN ?? 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Security constraint: Cannot delete the last remaining system administrator.');
            }
        }

        try {
            DB::transaction(function () use ($user) {
                // Log deletion BEFORE actually deleting to prevent foreign key errors if activities cascade
                Activity::create([
                    'user_id'     => Auth::id(),
                    'description' => "Permanently deleted user account: {$user->email}",
                ]);

                $user->delete();

                Cache::forget('admin_user_stats');
            });

            return back()->with('success', 'User account permanently deleted.');

        } catch (\Exception $e) {
            Log::error("User Deletion Error: " . $e->getMessage());
            return back()->with('error', 'A database constraint prevented the deletion of this user.');
        }
    }
}