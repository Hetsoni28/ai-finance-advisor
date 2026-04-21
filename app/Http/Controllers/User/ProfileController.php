<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Throwable;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | PROFILE DASHBOARD (IDENTITY HUB)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user instanceof User, 403, 'Unauthorized Node Access.');

        // 1. Core Financial Telemetry
        $totalIncome  = (float) $user->incomes()->sum('amount');
        $totalExpense = (float) $user->expenses()->sum('amount');
        $savings      = $totalIncome - $totalExpense;

        // 2. Cryptographic Audit Log
        $activities = Activity::where('causer_id', $user->id)
            ->latest('id')
            ->limit(15)
            ->get();

        // 3. 🔥 BEAST MODE: Fetch Real Active Sessions (Requires SESSION_DRIVER=database)
        $activeSessions = [];
        if (config('session.driver') === 'database') {
            $activeSessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) use ($request) {
                    $isActive = $session->id === $request->session()->getId();
                    return [
                        'device'   => $this->parseDevice($session->user_agent),
                        'browser'  => $this->parseBrowser($session->user_agent),
                        'ip'       => $session->ip_address,
                        'location' => 'Encrypted Node', // Can be replaced with GeoIP package
                        'active'   => $isActive,
                        'time'     => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                        'icon'     => str_contains(strtolower($session->user_agent), 'mobi') ? 'fa-mobile-screen' : 'fa-laptop',
                    ];
                })->toArray();
        }

        return view('user.profile.index', compact(
            'user',
            'totalIncome',
            'totalExpense',
            'savings',
            'activities',
            'activeSessions'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PROFILE
    |--------------------------------------------------------------------------
    */
    public function edit(): View
    {
        /** @var User $user */
        $user = Auth::user();
        
        return view('user.profile.edit', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PROFILE (IAM SYNCHRONIZATION)
    |--------------------------------------------------------------------------
    */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user, 403, 'Unauthorized Node Access.');

        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $user) {
                
                // 🚨 FIXED: Strict attribute filling to bypass mass-assignment vulnerability
                $user->fill($validated);

                // If email changes, force re-verification mathematically
                if ($user->isDirty('email')) {
                    $user->email_verified_at = null;
                    
                    // Optional: Send new verification email here
                    // $user->sendEmailVerificationNotification();
                }

                $user->save();

                activity()
                    ->causedBy($user)
                    ->performedOn($user)
                    ->log('Identity parameters synchronized and updated.');

            }, 3);

            return redirect()
                ->route('user.profile.index')
                ->with('success', 'Identity profile synchronized successfully.');

        } catch (Throwable $e) {
            Log::error('Profile Sync Failed: ' . $e->getMessage(), ['user_id' => $user->id]);
            
            $errorMsg = config('app.debug') 
                ? 'Sync failed: ' . $e->getMessage() 
                : 'System failure during profile update. Please contact support.';
                
            return back()->with('error', $errorMsg);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PASSWORD FORM
    |--------------------------------------------------------------------------
    */
    public function passwordForm(): View
    {
        return view('user.profile.password');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD & SECURITY
    |--------------------------------------------------------------------------
    */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user, 403);

        // 1. Verify current cryptographic authorization
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current security authorization failed.',
            ]);
        }

        try {
            // 2. Commit new credentials to the database
            DB::transaction(function () use ($request, $user) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);

                activity()
                    ->causedBy($user)
                    ->performedOn($user)
                    ->log('AES-256 Security credentials rotated.');
            }, 3);

            /*
            |--------------------------------------------------------------------------
            | 🚨 SECURE SESSION REGENERATION PROTOCOL
            |--------------------------------------------------------------------------
            | MUST happen outside the database transaction to prevent race conditions.
            | Requires \Illuminate\Session\Middleware\AuthenticateSession::class 
            | to be enabled in app/Http/Kernel.php (web middleware group).
            */
            Auth::logoutOtherDevices($request->password);
            $request->session()->regenerate();

            return redirect()
                ->route('user.profile.index')
                ->with('success', 'Credentials rotated and rogue sessions terminated.');

        } catch (Throwable $e) {
            Log::error('Credential Rotation Failed: ' . $e->getMessage(), ['user_id' => $user->id]);
            return back()->with('error', 'System failure during credential rotation. Ensure AuthenticateSession middleware is active.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | INTERNAL STRING PARSERS FOR USER AGENT
    |--------------------------------------------------------------------------
    */
    private function parseDevice(string $userAgent): string
    {
        if (str_contains($userAgent, 'Macintosh')) return 'MacBook / iMac';
        if (str_contains($userAgent, 'Windows')) return 'Windows Workstation';
        if (str_contains($userAgent, 'iPhone')) return 'iPhone';
        if (str_contains($userAgent, 'iPad')) return 'iPad';
        if (str_contains($userAgent, 'Android')) return 'Android Device';
        if (str_contains($userAgent, 'Linux')) return 'Linux Node';
        
        return 'Unknown Architecture';
    }

    private function parseBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Chrome') && !str_contains($userAgent, 'Edg')) return 'Google Chrome';
        if (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome')) return 'Apple Safari';
        if (str_contains($userAgent, 'Firefox')) return 'Mozilla Firefox';
        if (str_contains($userAgent, 'Edg')) return 'Microsoft Edge';
        
        return 'Unknown Client';
    }
}