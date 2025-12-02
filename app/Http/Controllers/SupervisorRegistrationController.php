<?php

namespace App\Http\Controllers;

use App\Mail\SupervisorVerificationEmail;
use App\Models\Company;
use App\Models\SupervisorProfile;
use App\Models\SupervisorRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SupervisorRegistrationController extends Controller
{
    /**
     * Show email input form
     */
    public function showEmailForm()
    {
        return view('supervisor.register.email');
    }

    /**
     * Send verification email
     */
    public function sendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->email;

        // Check if email already has a user account
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return back()->withErrors([
                'email' => 'An account with this email already exists. Please login instead.',
            ])->withInput();
        }

        // Delete any existing registration for this email
        SupervisorRegistration::where('email', $email)->delete();

        // Create new registration
        $registration = SupervisorRegistration::create([
            'email' => $email,
            'token' => SupervisorRegistration::generateToken(),
            'expires_at' => Carbon::now()->addHours(24), // 24 hour expiration
        ]);

        // Send verification email
        try {
            Mail::to($email)->send(new SupervisorVerificationEmail($registration));

            return view('supervisor.register.email-sent', compact('email'));
        } catch (\Exception $e) {
            \Log::error('Failed to send supervisor verification email: '.$e->getMessage());

            return back()->with('error', 'Failed to send verification email. Please try again.');
        }
    }

    /**
     * Verify token and show complete registration form
     */
    public function verify($token)
    {
        $registration = SupervisorRegistration::where('token', $token)->first();

        if (! $registration) {
            return view('supervisor.register.link-expired', [
                'reason' => 'invalid',
                'email' => null,
            ]);
        }

        // Check if email already has a user account (someone registered while link was pending)
        $existingUser = User::where('email', $registration->email)->first();
        if ($existingUser) {
            // Delete the registration since account already exists
            $registration->delete();

            return view('supervisor.register.error', [
                'error' => 'Account Already Exists',
                'message' => 'An account with this email already exists. Please login to your account.',
            ]);
        }

        if ($registration->isExpired()) {
            return view('supervisor.register.link-expired', [
                'reason' => 'expired',
                'email' => $registration->email,
            ]);
        }

        if ($registration->isVerified()) {
            return view('supervisor.register.error', [
                'error' => 'Already Verified',
                'message' => 'This email has already been verified. Please complete your registration or login if you already have an account.',
            ]);
        }

        // Mark as verified
        $registration->markAsVerified();

        return view('supervisor.register.complete', compact('registration'));
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->email;

        // Check if email already has a user account
        if (User::where('email', $email)->exists()) {
            return back()->with('error', 'An account with this email already exists. Please login instead.');
        }

        // Find existing registration
        $registration = SupervisorRegistration::where('email', $email)->first();

        if ($registration) {
            // Update existing registration with new token and expiration
            $registration->update([
                'token' => SupervisorRegistration::generateToken(),
                'expires_at' => Carbon::now()->addHours(24),
                'verified_at' => null, // Reset verification
            ]);
        } else {
            // Create new registration
            $registration = SupervisorRegistration::create([
                'email' => $email,
                'token' => SupervisorRegistration::generateToken(),
                'expires_at' => Carbon::now()->addHours(24),
            ]);
        }

        // Send verification email
        try {
            Mail::to($email)->send(new SupervisorVerificationEmail($registration));

            return back()->with('status', 'A new verification link has been sent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend supervisor verification email: '.$e->getMessage());

            return back()->with('error', 'Failed to send verification email. Please try again.');
        }
    }

    /**
     * Complete registration and create account
     */
    public function complete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:supervisor_registrations,token',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $registration = SupervisorRegistration::where('token', $request->token)
            ->where('verified_at', '!=', null)
            ->first();

        if (! $registration) {
            return redirect()->route('supervisor.register')->with('error', 'Invalid or unverified registration token. Please start the registration process again.');
        }

        // Check if user already exists (race condition protection)
        $existingUser = User::where('email', $registration->email)->first();
        if ($existingUser) {
            // Delete the registration since account already exists
            $registration->delete();

            return redirect()->route('login')->with('error', 'An account with this email already exists. Please login instead.');
        }

        try {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $registration->email,
                'password' => Hash::make($request->password),
                'role' => 'supervisor',
                'email_verified_at' => now(),
            ]);

            // Create or find company
            $company = Company::firstOrCreate(
                ['name' => $request->company_name],
                [
                    'address' => $request->company_address,
                    'contact_person' => $request->name,
                    'contact_email' => $registration->email,
                    'contact_phone' => $request->phone ?? '',
                ]
            );

            // Create supervisor profile
            SupervisorProfile::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'position' => $request->position,
                'phone' => $request->phone,
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            // Delete the registration record (no longer needed)
            $registration->delete();

            // Log the user in
            Auth::login($user);
            $request->session()->regenerate(); // Regenerate session to prevent fixation

            return redirect()->route('dashboard')->with('success', 'Account created successfully! You can now search for students to accept.');
        } catch (\Exception $e) {
            \Log::error('Failed to create supervisor account: '.$e->getMessage());

            return back()->with('error', 'Failed to create account. Please try again.');
        }
    }
}
