<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Family;
use App\Models\FamilyInvite;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        // ✅ Normalize email
        $email = strtolower(trim($validated['email']));

        $user = new User();
        $user->name       = trim($validated['name']);
        $user->email      = $email;
        $user->password   = Hash::make($validated['password']);
        $user->role       = 'user';
        $user->is_admin   = false;
        $user->is_blocked = false;
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        $redirect = $this->handleInviteFlow($user);

        // ✅ AJAX response for frontend engine
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Account created successfully.',
                'redirect' => $redirect->getTargetUrl(),
            ]);
        }

        return $redirect;
    }

    /* =====================================
       LOGIN USER
    ====================================== */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        // ✅ Normalize email SAME as register
        $email = strtolower(trim($validated['email']));

        // ✅ Fetch user manually (fix case mismatch issue)
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            // AJAX: return 422 JSON with error
            if ($request->ajax() || $request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid email or password.'],
                ]);
            }
            return back()->withErrors([
                'email' => 'Invalid email or password.'
            ])->onlyInput('email');
        }

        // 🚫 Block check BEFORE login
        if ($user->is_blocked) {
            if ($request->ajax() || $request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => ['Your account has been blocked.'],
                ]);
            }
            return back()->withErrors([
                'email' => 'Your account has been blocked.'
            ]);
        }

        // ✅ Login manually
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $redirect = $this->handleInviteFlow($user);

        // ✅ AJAX response for frontend engine
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Login successful.',
                'redirect' => $redirect->getTargetUrl(),
            ]);
        }

        return $redirect;
    }

    /* =====================================
       LOGOUT
    ====================================== */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /* =====================================
       INVITE FLOW — AUTO-JOIN FAMILY
    ====================================== */
    protected function handleInviteFlow(User $user)
    {
        $token    = session('pending_invite_token');
        $familyId = session('pending_family_id');

        // No pending invite → normal redirect
        if (!$token || !$familyId) {
            return $this->redirectAfterLogin($user);
        }

        // Clear session data immediately to prevent replay
        session()->forget(['pending_invite_token', 'pending_family_id', 'invited_email']);

        try {
            // Look up the invite
            $invite = FamilyInvite::where('token', $token)
                ->where('family_id', $familyId)
                ->first();

            if (!$invite) {
                return redirect()->route('user.dashboard')
                    ->with('error', 'Invalid invitation token. The link may have been revoked.');
            }

            // Check if already consumed
            if ($invite->accepted_at) {
                return redirect()->route('user.dashboard')
                    ->with('info', 'This invitation has already been used.');
            }

            // Check if expired
            if ($invite->expires_at && \Carbon\Carbon::parse($invite->expires_at)->isPast()) {
                return redirect()->route('user.dashboard')
                    ->with('error', 'This invitation has expired. Please request a new one.');
            }

            // Check if already a member
            $family = Family::find($familyId);
            if (!$family) {
                return redirect()->route('user.dashboard')
                    ->with('error', 'The workspace no longer exists.');
            }

            if ($family->users()->where('user_id', $user->id)->exists()) {
                return redirect()->route('user.families.show', $family->id)
                    ->with('info', 'You are already a member of this workspace.');
            }

            // ✅ Execute the join inside a transaction
            DB::transaction(function () use ($invite, $user, $family) {
                FamilyMember::create([
                    'family_id' => $family->id,
                    'user_id'   => $user->id,
                    'role'      => 'member',
                ]);

                $invite->update([
                    'accepted_at' => now(),
                    'accepted_by' => $user->id,
                ]);

                activity()
                    ->causedBy($user)
                    ->performedOn($family)
                    ->log('Node accepted workspace invitation and joined via registration.');
            });

            return redirect()->route('user.families.show', $family->id)
                ->with('success', 'Welcome! You have successfully joined the workspace.');

        } catch (\Throwable $e) {
            Log::error('Invite Auto-Join Failed: ' . $e->getMessage(), [
                'user_id'   => $user->id,
                'family_id' => $familyId,
                'token'     => substr($token, 0, 8) . '...',
            ]);

            return redirect()->route('user.dashboard')
                ->with('error', 'Failed to join workspace. Please contact the workspace owner.');
        }
    }

    /* =====================================
       REDIRECT
    ====================================== */
    protected function redirectAfterLogin(User $user)
    {
        return ($user->is_admin || $user->role === 'admin')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
}