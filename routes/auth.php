<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Notifications\VerifyWithTemporaryPassword;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

// Email Verification Notice - Accessible without auth (for new registrations)
Route::get('/verify-email', function () {
    return view('auth.verify-email');
})->name('verification.notice');

// Expired verification page - Accessible without auth
Route::get('/verification-expired', function () {
    return view('auth.verification-expired');
})->name('verification.expired');

// Resend verification - Accessible without auth (for expired links)
Route::post('/verification-resend', function (Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email'
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();
    
    if (!$user) {
        return back()->withErrors(['email' => 'User not found.']);
    }

    // For coordinators/supervisors who were created by admin, use custom notification
    if (in_array($user->role, ['coordinator', 'supervisor'])) {
        // Generate new temporary password for resend
        $temporaryPassword = \Illuminate\Support\Str::random(12);
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($temporaryPassword),
            'email_verified_at' => null, // Reset verification status
            'must_change_password' => true // Ensure they must change password
        ]);
        
        $user->notify(new \App\Notifications\VerifyWithTemporaryPassword($temporaryPassword));
    } else {
        // For students and others, use default verification
        $user->update(['email_verified_at' => null]); // Reset verification status
        $user->sendEmailVerificationNotification();
    }
    
    return back()->with('status', 'New verification link sent to your email! Please check your inbox and click the link to verify your account.');
})->name('verification.resend');

// Email Verification Routes (no auth required)
Route::get('/verify-email/{id}/{hash}', function (Request $request) {
    // Check if the signature is valid first
    if (!$request->hasValidSignature()) {
        return redirect()->route('verification.expired');
    }

    // Get the user
    $user = \App\Models\User::findOrFail($request->route('id'));
    
    // Verify the hash matches
    if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        return redirect()->route('verification.expired');
    }

    // For coordinators/supervisors, redirect to login with message
    if (in_array($user->role, ['coordinator', 'supervisor'])) {
        // Log out any existing session to ensure clean login
        \Illuminate\Support\Facades\Auth::logout();
        \Illuminate\Support\Facades\Session::flush();
        
        return redirect()->route('login')->with('status', 'Verification link valid! Please login with your temporary password to verify your email and change it on first login.');
    }

    // For students/admins, mark email as verified and proceed to dashboard
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // Students/Admins proceed to dashboard
    return redirect()->route('dashboard');
})->name('verification.verify');

Route::middleware('auth')->group(function () {

    Route::post('/email/verification-notification', function (Request $request) {
        $user = $request->user();
        
        // For coordinators/supervisors who were created by admin, use custom notification
        if (in_array($user->role, ['coordinator', 'supervisor'])) {
            // Generate new temporary password for resend
            $temporaryPassword = Str::random(12);
            $user->update(['password' => Hash::make($temporaryPassword)]);
            
            $user->notify(new VerifyWithTemporaryPassword($temporaryPassword));
        } else {
            // For students and others, use default verification
            $user->sendEmailVerificationNotification();
        }
        
        return back()->with('status', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
