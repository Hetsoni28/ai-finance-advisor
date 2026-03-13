<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* =====================================
       SHOW LOGIN PAGE
    ====================================== */
    public function loginPage()
    {
        if (Auth::check() && ! session()->has('invite_token')) {
            return $this->redirectAfterLogin(Auth::user());
        }

        return view('auth.login');
    }

    /* =====================================
       SHOW REGISTER PAGE
    ====================================== */
    public function registerPage()
    {
        if (Auth::check() && ! session()->has('invite_token')) {
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
            'password' => ['required','min:6','confirmed'],
        ]);

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => strtolower($validated['email']),
            'password'   => Hash::make($validated['password']),
            'role'       => 'user',
            'is_admin'   => false,
            'is_blocked' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->handleInviteFlow();
    }

    /* =====================================
       LOGIN USER
    ====================================== */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        $credentials = [
            'email'    => strtolower($validated['email']),
            'password' => $validated['password'],
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Block protection
        if ($user->is_blocked) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Your account has been blocked. Contact administrator.'
            ]);
        }

        return $this->handleInviteFlow();
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
       INVITE FLOW HANDLER
    ====================================== */
    protected function handleInviteFlow()
    {
        if (session()->has('invite_token')) {

            $token = session()->pull('invite_token');

            // TODO: Replace this logic with actual token lookup
            return redirect()->route('user.dashboard')
                ->with('info', 'Invitation token detected. Please complete invitation process.');
        }

        return $this->redirectAfterLogin(Auth::user());
    }

    /* =====================================
       ROLE BASED REDIRECT
    ====================================== */
    protected function redirectAfterLogin(User $user)
    {
        if ($user->is_admin || $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }
}