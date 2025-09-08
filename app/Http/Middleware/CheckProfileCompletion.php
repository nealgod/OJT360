<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Skip profile check for admin users
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Check if user has completed their profile
        if ($user && !$this->hasCompletedProfile($user)) {
            // Allow access to profile edit and logout routes
            if ($request->routeIs('profile.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            // Redirect to profile completion
            return redirect()->route('profile.edit')->with('warning', 'Please complete your profile to access all features.');
        }

        return $next($request);
    }

    /**
     * Check if user has completed their profile.
     */
    private function hasCompletedProfile($user): bool
    {
        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            return $profile && 
                   $profile->student_id && 
                   $profile->course && 
                   $profile->department;
        }

        if ($user->isCoordinator()) {
            $profile = $user->coordinatorProfile;
            return $profile && 
                   $profile->employee_id && 
                   $profile->department;
        }

        if ($user->isSupervisor()) {
            $profile = $user->supervisorProfile;
            return $profile && 
                   $profile->company_id;
        }

        return true; // Admin users don't need profile completion
    }
}
