<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // For coordinators/supervisors, verify email on successful login
        $user = Auth::user();
        if (in_array($user->role, ['coordinator', 'supervisor']) && !$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Check if user must change password
        if ($user->must_change_password) {
            return redirect()->route('password.first-change');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout current session
        Auth::logout();
        
        // Invalidate current session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
