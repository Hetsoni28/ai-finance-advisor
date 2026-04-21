<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Show All Plans
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = Auth::user();
        $plans = Plan::active()->get();

        // Get user's current active subscription (auto-expire check)
        $subscription = $this->getActiveSubscription($user);
        $currentPlan = $subscription?->plan;
        $currentPlanSlug = $currentPlan?->slug ?? 'free';

        return view('user.subscription.index', [
            'plans'           => $plans,
            'subscription'    => $subscription,
            'currentPlan'     => $currentPlan,
            'currentPlanSlug' => $currentPlanSlug,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SUBSCRIBE — Dummy Payment + Assign Plan
    |--------------------------------------------------------------------------
    */
    public function subscribe(Request $request, $planId)
    {
        $user = Auth::user();
        $plan = Plan::findOrFail($planId);

        // Cannot subscribe to the same plan
        $currentSub = $this->getActiveSubscription($user);
        if ($currentSub && $currentSub->plan_id === $plan->id) {
            return back()->with('error', 'You are already subscribed to this plan.');
        }

        // Free plan downgrade
        if ($plan->isFree()) {
            return $this->downgradeToFree($user);
        }

        try {
            DB::transaction(function () use ($user, $plan) {

                // Cancel any existing active subscription
                UserSubscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled']);

                // Generate dummy payment ID
                $paymentId = 'PAY-' . strtoupper(Str::random(12));

                // Create new subscription
                UserSubscription::create([
                    'user_id'     => $user->id,
                    'plan_id'     => $plan->id,
                    'start_date'  => now(),
                    'end_date'    => now()->addDays($plan->duration_days),
                    'status'      => 'active',
                    'payment_id'  => $paymentId,
                    'amount_paid' => $plan->price,
                ]);

                // Log the activity
                if (function_exists('activity')) {
                    activity()
                        ->causedBy($user)
                        ->withProperties(['plan' => $plan->name, 'amount' => $plan->price])
                        ->log("Subscribed to {$plan->name} plan.");
                }
            });

            return redirect()
                ->route('user.subscription.success')
                ->with('subscribed_plan', $plan->name);

        } catch (\Throwable $e) {
            Log::error('Subscription failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SUCCESS — Post Payment Confirmation
    |--------------------------------------------------------------------------
    */
    public function success()
    {
        $planName = session('subscribed_plan', 'your selected plan');
        return view('user.subscription.success', compact('planName'));
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL — Downgrade to Free
    |--------------------------------------------------------------------------
    */
    public function cancel()
    {
        $user = Auth::user();
        return $this->downgradeToFree($user);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Get user's active subscription, auto-expire if needed.
     */
    private function getActiveSubscription($user): ?UserSubscription
    {
        $subscription = UserSubscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('created_at')
            ->first();

        if ($subscription && $subscription->checkAndExpire()) {
            // Was expired, return null (now on free)
            return null;
        }

        return $subscription;
    }

    /**
     * Downgrade user to free plan.
     */
    private function downgradeToFree($user)
    {
        // Cancel all active subscriptions
        UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Assign free plan
        $freePlan = Plan::where('slug', 'free')->first();

        if ($freePlan) {
            UserSubscription::create([
                'user_id'     => $user->id,
                'plan_id'     => $freePlan->id,
                'start_date'  => now(),
                'end_date'    => null, // Lifetime
                'status'      => 'active',
                'payment_id'  => null,
                'amount_paid' => 0,
            ]);
        }

        return redirect()
            ->route('user.subscription.index')
            ->with('success', 'You have been downgraded to the Starter plan.');
    }
}
