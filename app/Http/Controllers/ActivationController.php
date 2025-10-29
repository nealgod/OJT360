<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentWhitelist;
use App\Models\StudentVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentVerificationMail;

class ActivationController extends Controller
{
    public function showVerifyId()
    {
        return view('auth.verify-student');
    }

    public function sendVerification(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string'],
        ]);

        $row = EnrollmentWhitelist::where('student_id', $validated['student_id'])
            ->where('status', 'pending')
            ->first();

        if (!$row) {
            return back()->withErrors(['student_id' => 'Student ID not found or already activated.'])->withInput();
        }

        // Prevent spamming: reuse existing unexpired token if exists
        $existing = StudentVerification::where('student_id', $row->student_id)
            ->where('expires_at', '>', now())
            ->first();

        if (!$existing) {
            $existing = StudentVerification::create([
                'student_id' => $row->student_id,
                'token' => Str::random(64),
                'expires_at' => now()->addMinutes(60),
            ]);
        }

        $link = URL::route('student.complete.show', ['token' => $existing->token]);

        // Send mail to the email from whitelist
        Mail::to($row->email)->send(new StudentVerificationMail($row->name, $row->student_id, $link));

        return back()->with('status', 'Verification link sent to your school email. Please check your inbox.');
    }

    public function showComplete(string $token)
    {
        // Fetch record by token regardless of expiry to handle friendly expired view
        $recordAny = StudentVerification::where('token', $token)->first();

        if (!$recordAny) {
            return view('auth.link-expired', [
                'studentId' => null,
                'name' => null,
                'email' => null,
                'reason' => 'invalid',
            ]);
        }

        // If expired, show expired view with quick resend
        if ($recordAny->expires_at->isPast()) {
            $row = EnrollmentWhitelist::where('student_id', $recordAny->student_id)->first();
            return view('auth.link-expired', [
                'studentId' => $recordAny->student_id,
                'name' => $row?->name,
                'email' => $row?->email,
                'reason' => 'expired',
            ]);
        }

        // Valid token
        $row = EnrollmentWhitelist::where('student_id', $recordAny->student_id)
            ->where('status', 'pending')
            ->firstOrFail();

        return view('auth.complete-registration', [
            'studentId' => $row->student_id,
            'name' => $row->name,
            'email' => $row->email,
            'token' => $token,
        ]);
    }

    public function completeRegistration(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
            'phone' => ['nullable', 'string'],
        ]);

        $record = StudentVerification::where('token', $validated['token'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'The verification link is invalid or expired.']);
        }

        $row = EnrollmentWhitelist::where('student_id', $record->student_id)
            ->where('status', 'pending')
            ->first();

        if (!$row) {
            return back()->withErrors(['token' => 'Student record not available for activation.']);
        }

        if (User::where('email', $row->email)->exists()) {
            return back()->withErrors(['email' => 'Account already exists for this email. Try logging in.']);
        }

        $user = User::create([
            'name' => $row->name,
            'email' => $row->email,
            'password' => Hash::make($validated['password']),
            'role' => 'intern',
        ]);

        $user->studentProfile()->create([
            'student_id' => $row->student_id,
            'course' => $row->program?->name,
            'department' => $row->program?->department?->name,
            'phone' => $validated['phone'] ?? $row->contact_number,
        ]);

        $row->update(['status' => 'activated']);

        // Invalidate token
        $record->delete();

        // Auto-verify email for OJT registration flow
        $user->forceFill(['email_verified_at' => now()])->save();

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome to OJT360! Your account is ready.');
    }
    public function show()
    {
        return view('auth.activate');
    }

    public function activate(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $email = strtolower($request->email);
        if (!str_ends_with($email, '@evsu.edu.ph')) {
            return back()->withErrors(['email' => 'Only EVSU emails are allowed.'])->withInput();
        }

        $row = EnrollmentWhitelist::where('student_id', $request->student_id)
            ->where('email', $email)
            ->where('status', 'pending')
            ->first();

        if (!$row) {
            return back()->withErrors(['student_id' => 'No matching pending record found. Please contact your coordinator.'])->withInput();
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            return back()->withErrors(['email' => 'Account already exists. Try logging in or reset password.']);
        }

        $user = User::create([
            'name' => $row->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => 'intern',
        ]);

        // Minimal student profile, auto-filled from whitelist (including phone)
        $user->studentProfile()->create([
            'student_id' => $row->student_id,
            'course' => $row->program?->name,
            'department' => $row->program?->department?->name,
            'phone' => $row->contact_number,
        ]);

        $row->update(['status' => 'activated']);

        // Send email verification
        $user->sendEmailVerificationNotification();

        // Auto-login
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created. Please verify your email from your inbox.');
    }
}


