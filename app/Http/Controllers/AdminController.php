<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        return redirect()->route('dashboard');
    }

    public function users(Request $request)
    {
        $query = User::with([
            'studentProfile.program',
            'studentProfile.supervisor.supervisorProfile.company',
            'coordinatorProfile.department', 
            'coordinatorProfile.program', 
            'supervisorProfile.company'
        ]);

        // Filter by role if provided
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get counts for quick stats
        $stats = [
            'total' => User::count(),
            'coordinators' => User::where('role', 'coordinator')->count(),
            'supervisors' => User::where('role', 'supervisor')->count(),
            'students' => User::where('role', 'intern')->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    public function createUser()
    {
        $departments = Department::with('programs:id,department_id,name')
            ->get(['id', 'name']);

        return view('admin.create-user', compact('departments'));
    }

    public function storeUser(Request $request)
    {
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['required', 'in:coordinator,supervisor'],
        ];

        if ($request->role === 'coordinator') {
            $rules['department_id'] = ['required', 'exists:departments,id'];
            $rules['program_id'] = ['required', 'exists:programs,id'];
        }

        $validated = $request->validate($rules);

        // Coordinator: send invitation link and do not create user yet
        if ($request->role === 'coordinator') {
            if (User::where('email', $request->email)->exists()) {
                return back()->withErrors(['email' => 'Email is already in use by another account.'])->withInput();
            }

            $token = Str::random(64);
            $invite = \App\Models\CoordinatorInvitation::create([
                'email' => strtolower($request->email),
                'token' => $token,
                'department_id' => $request->integer('department_id'),
                'program_id' => $request->integer('program_id'),
                'expires_at' => now()->addHour(),
            ]);

            $link = \Illuminate\Support\Facades\URL::route('coordinator.complete.show', ['token' => $invite->token]);
            try {
                \Illuminate\Support\Facades\Mail::to($invite->email)->send(new \App\Mail\CoordinatorInvitationMail($link));
            } catch (\Exception $e) {
                \Log::error('Coordinator invite email failed: '.$e->getMessage());
            }

            return redirect()->route('admin.users')->with('success', 'Coordinator invite sent successfully.');
        }

        // Supervisor: send registration invitation (like coordinator flow)
        if (User::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'Email is already in use by another account.'])->withInput();
        }

        // Check if there's already a pending registration
        $existingRegistration = \App\Models\SupervisorRegistration::where('email', strtolower($request->email))->first();

        if ($existingRegistration) {
            // Update existing registration with new token and expiration
            $existingRegistration->update([
                'token' => \App\Models\SupervisorRegistration::generateToken(),
                'expires_at' => \Carbon\Carbon::now()->addHours(24),
                'verified_at' => null,
            ]);
            $registration = $existingRegistration;
        } else {
            // Create new registration
            $registration = \App\Models\SupervisorRegistration::create([
                'email' => strtolower($request->email),
                'token' => \App\Models\SupervisorRegistration::generateToken(),
                'expires_at' => \Carbon\Carbon::now()->addHours(24),
            ]);
        }

        // Send verification email
        try {
            \Illuminate\Support\Facades\Mail::to($registration->email)->send(new \App\Mail\SupervisorVerificationEmail($registration));
        } catch (\Exception $e) {
            \Log::error('Supervisor invite email failed: '.$e->getMessage());
        }

        return redirect()->route('admin.users')->with('success', 'Supervisor registration invitation sent successfully.');
    }
}
