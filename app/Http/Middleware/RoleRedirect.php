<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'owner') {
                return redirect()->route('owner.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}

