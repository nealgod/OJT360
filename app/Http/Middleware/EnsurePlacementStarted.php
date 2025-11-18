<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePlacementStarted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isStudent()) {
            return $next($request);
        }

        $profile = $user->studentProfile;

        if (!$profile || !$profile->preplacement_complete) {
            return redirect()->route('documents.index')
                ->with('error', 'Please complete all required pre-placement documents to unlock this feature.');
        }
        return $next($request);
    }
}


