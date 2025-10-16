<?php

namespace App\Http\Middleware;

use App\Models\PlacementRequest;
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

        // Simplify guard: rely on active OJT status only
        if (!$user->studentProfile || $user->studentProfile->ojt_status !== 'active') {
            return redirect()->back()->with('error', 'Your OJT placement is not active yet. Please wait for coordinator approval.');
        }
        return $next($request);
    }
}


