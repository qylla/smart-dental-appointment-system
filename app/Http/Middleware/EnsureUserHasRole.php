<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Restricts a route to one or more roles.
     *
     * Usage in routes:  ->middleware('role:admin')
     *                   ->middleware('role:admin,doctor')
     *
     * Rather than showing a bare 403, a signed-in user who hits the wrong
     * area is sent back to their own dashboard, which is less confusing.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('status', 'Please log in to continue.');
        }

        $user = Auth::user();

        if (! in_array($user->role, $roles, true)) {
            return redirect(\App\Http\Controllers\AuthController::homeFor($user))
                ->with('status', 'You do not have access to that area.');
        }

        return $next($request);
    }
}