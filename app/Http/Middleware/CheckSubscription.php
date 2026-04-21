<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Plan;
use App\Models\UserSubscription;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     * 
     * Usage in routes:
     *   ->middleware('subscription:pro')       // Requires Pro or higher
     *   ->middleware('subscription:premium')    // Requires Premium only
     *   ->middleware('subscription')            // Requires any paid plan
     *
     * @param string|null $minimumPlan  The minimum plan slug required (pro / premium)
     */
    public function handle(Request $request, Closure $next, ?string $minimumPlan = null)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Get active subscription
        $subscription = UserSubscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('created_at')
            ->first();

        // Auto-expire check
        if ($subscription && $subscription->isExpired()) {
            $subscription->update(['status' => 'expired']);
            $subscription = null;
        }

        $currentSlug = $subscription?->plan?->slug ?? 'free';

        // Define plan hierarchy
        $planHierarchy = [
            'free'    => 0,
            'pro'     => 1,
            'premium' => 2,
        ];

        $currentLevel = $planHierarchy[$currentSlug] ?? 0;
        $requiredLevel = $planHierarchy[$minimumPlan ?? 'pro'] ?? 1;

        if ($currentLevel < $requiredLevel) {
            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error'   => 'Upgrade your plan to access this feature.',
                    'upgrade' => true,
                ], 403);
            }

            return redirect()
                ->route('user.subscription.index')
                ->with('error', 'Upgrade your plan to access this feature.');
        }

        return $next($request);
    }
}
