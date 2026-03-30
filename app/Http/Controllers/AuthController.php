<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FamilyInvite;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    /* =====================================
       SHOW LOGIN PAGE
    ====================================== */
    public function loginPage()
    {
        if (Auth::check() && !session()->has('pending_invite_token')) {
            return $this->redirectAfterLogin(Auth::user());
        }

        return view('auth.login');
    }

    /* =====================================
       SHOW REGISTER PAGE
    ====================================== */
    public function registerPage()
    {
        if (Auth::check() && !session()->has('pending_invite_token')) {
            return $this->redirectAfterLogin(Auth::user());
        }

        return view('auth.register');
    }

    /* =====================================
       REGISTER USER
    ====================================== */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        // ✅ Normalize email
        $email = strtolower(trim($validated['email']));

        // 🚨 FIXED: Stripped protected attributes to respect Model security
        $user = User::create([
            'name'     => trim($validated['name']),
            'email'    => $email,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->handleInviteFlow($user);
    }

    /* =====================================
       LOGIN USER
    ====================================== */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $credentials = [
            'email'    => strtolower(trim($validated['email'])),
            'password' => $validated['password'],
        ];

        // 🚫 Block check BEFORE full login attempt
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && $user->isBlocked()) {
            return back()->withErrors([
                'email' => 'Security Protocol: Your network access has been revoked.'
            ]);
        }

        // ✅ Use Native Laravel Auth Attempt for maximum security
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->handleInviteFlow(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Invalid credentials provided.'
        ])->onlyInput('email');
    }

    /* =====================================
       LOGOUT
    ====================================== */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /* =====================================
       🔥 BEAST MODE: INVITE HANDSHAKE INTERCEPT
    ====================================== */
    protected function handleInviteFlow(User $user): RedirectResponse
    {
        // 1. Check if they arrived via a Cryptographic Email Link
        if (session()->has('pending_invite_token') && session()->has('pending_family_id')) {
            
            $token    = session('pending_invite_token');
            $familyId = session('pending_family_id');

            try {
                $invite = FamilyInvite::where('token', $token)
                    ->where('family_id', $familyId)
                    ->whereNull('accepted_at') // Failsafe: Ensure it wasn't used mid-auth
                    ->first();

                if ($invite) {
                    DB::transaction(function () use ($invite, $user, $familyId) {
                        // Attach to Workspace
                        FamilyMember::create([
                            'family_id' => $familyId,
                            'user_id'   => $user->id,
                            'role'      => 'member',
                        ]);

                        // Burn the token
                        $invite->update([
                            'accepted_at' => now(),
                            'accepted_by' => $user->id
                        ]);
                    });

                    // Purge session variables
                    session()->forget(['pending_invite_token', 'pending_family_id', 'invited_email']);

                    // DIRECT DROP into the Hub!
                    return redirect()->route('user.families.show', $familyId)
                        ->with('success', 'Authentication successful. You have been securely integrated into the workspace.');
                }
            } catch (Throwable $e) {
                Log::error('Auth Invite Integration Failed: ' . $e->getMessage());
                // Silently fail to standard dashboard if database errors occur
            }

            // Purge dead session variables
            session()->forget(['pending_invite_token', 'pending_family_id', 'invited_email']);
        }

        // 2. Standard Redirect if no invite was present
        return $this->redirectAfterLogin($user);
    }

    /* =====================================
       ROUTING ENGINE
    ====================================== */
    protected function redirectAfterLogin(User $user): RedirectResponse
    {
        // Utilize the secure Model method we built previously
        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
}