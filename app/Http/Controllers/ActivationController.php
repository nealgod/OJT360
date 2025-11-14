<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentWhitelist;
use App\Models\StudentVerification;
use App\Models\CoordinatorInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentVerificationMail;
use App\Mail\CoordinatorInvitationMail;

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
        $row = EnrollmentWhitelist::with(['program.department'])->where('student_id', $recordAny->student_id)
            ->where('status', 'pending')
            ->firstOrFail();

        return view('auth.complete-registration', [
            'studentId' => $row->student_id,
            'name' => $row->name,
            'email' => $row->email,
            'token' => $token,
            'department' => $row->program?->department?->name,
            'program' => $row->program?->name,
        ]);
    }

    public function completeRegistration(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
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
            'address' => $validated['address'],
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
            'address' => $row->address ?? null,
        ]);

        $row->update(['status' => 'activated']);

        // Send email verification
        $user->sendEmailVerificationNotification();

        // Auto-login
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created. Please verify your email from your inbox.');
    }

    // Coordinator: show complete form from invite token
    public function showCompleteCoordinator(string $token)
    {
        $invite = CoordinatorInvitation::with(['department', 'program'])
            ->where('token', $token)
            ->first();

        if (!$invite) {
            return view('auth.link-expired', [
                'studentId' => null,
                'name' => null,
                'email' => null,
                'reason' => 'invalid',
            ]);
        }

        if ($invite->expires_at->isPast()) {
            return view('auth.link-expired', [
                'studentId' => null,
                'name' => null,
                'email' => $invite->email,
                'reason' => 'expired',
            ]);
        }

        return view('auth.complete-coordinator', [
            'email' => $invite->email,
            'token' => $token,
            'department' => $invite->department?->name,
            'program' => $invite->program?->name,
        ]);
    }

    // Coordinator: complete registration, create user and profile
    public function completeCoordinator(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:255', 'unique:coordinator_profiles,employee_id'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $invite = CoordinatorInvitation::where('token', $validated['token'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$invite) {
            return back()->withErrors(['token' => 'The invitation link is invalid or expired.']);
        }

        // Create or find user by email
        $existing = User::where('email', $invite->email)->first();
        if ($existing) {
            return back()->withErrors(['email' => 'An account with this email already exists.']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $invite->email,
            'password' => Hash::make($validated['password']),
            'role' => 'coordinator',
            'must_change_password' => false,
        ]);

        // Mark email as verified explicitly (fillable doesn't include email_verified_at)
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Create coordinator profile (populate department string for read-only display and phone)
        \App\Models\CoordinatorProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'department_id' => $invite->department_id,
                'program_id' => $invite->program_id,
                'department' => $invite->department?->name,
                'employee_id' => $validated['employee_id'],
                'phone' => $validated['phone'],
                'status' => 'active',
            ]
        );

        // Consume invitation
        $invite->delete();

        Auth::login($user);
        return redirect()->route('dashboard');
    }

    // Coordinator: resend invite by email when link expired (no admin needed)
    public function resendCoordinatorInvite(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $invite = CoordinatorInvitation::where('email', strtolower($validated['email']))
            ->orderByDesc('created_at')
            ->first();

        if (!$invite) {
            return back()->withErrors(['email' => 'No pending invitation found for this email. Please contact your admin.']);
        }

        // Refresh token and expiry
        $invite->update([
            'token' => Str::random(64),
            'expires_at' => now()->addHour(),
        ]);

        $link = URL::route('coordinator.complete.show', ['token' => $invite->token]);
        try {
            Mail::to($invite->email)->send(new CoordinatorInvitationMail($link));
        } catch (\Exception $e) {
            \Log::error('Coordinator invite resend failed: ' . $e->getMessage());
        }

        return back()->with('status', 'A new invitation link has been sent to your email.');
    }
}


