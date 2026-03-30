<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {

                $user = Auth::guard($guard)->user();

                // Prevent redirect loop
                if ($request->routeIs('user.dashboard') || $request->routeIs('admin.dashboard')) {
                    return $next($request);
                }

                // Admin redirect
                if ($user->is_admin || $user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                // User redirect
                return redirect()->route('user.dashboard');
            }
        }

        return $next($request);
    }
}