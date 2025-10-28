<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentWhitelist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ActivationController extends Controller
{
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

        // Minimal student profile
        $user->studentProfile()->create([
            'student_id' => $row->student_id,
            'course' => $row->program?->name,
            'department' => $row->program?->department?->name,
        ]);

        $row->update(['status' => 'activated']);

        // Send email verification
        $user->sendEmailVerificationNotification();

        // Auto-login
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created. Please verify your email from your inbox.');
    }
}


