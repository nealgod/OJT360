<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            // Allowlist routes that must remain accessible
            if ($request->routeIs([
                'password.first-change',
                'password.first-change.update',
                'verification.*',
                'logout',
            ])) {
                return $next($request);
            }

            return redirect()->route('password.first-change');
        }

        return $next($request);
    }
}


