<?php

namespace App\Http\Controllers;

use App\Mail\SupervisorVerificationEmail;
use App\Models\SupervisorRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CoordinatorSupervisorController extends Controller
{
    public function index()
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        $programName = optional($coordinator->coordinatorProfile?->program)->name;
        $programFilter = $programName ? strtolower($programName) : null;

        $supervisors = User::where('role', 'supervisor')
            ->whereHas('studentProfiles', function ($query) use ($department, $programFilter) {
                $query->where('department', $department);
                if ($programFilter) {
                    $query->whereRaw('LOWER(course) = ?', [$programFilter]);
                }
            })
            ->with([
                'supervisorProfile.company',
                'studentProfiles' => function ($query) use ($department, $programFilter) {
                    $query->where('department', $department);
                    if ($programFilter) {
                        $query->whereRaw('LOWER(course) = ?', [$programFilter]);
                    }
                    $query->with('user');
                },
            ])
            ->withCount([
                'studentProfiles as managed_students_count' => function ($query) use ($department, $programFilter) {
                    $query->where('department', $department);
                    if ($programFilter) {
                        $query->whereRaw('LOWER(course) = ?', [$programFilter]);
                    }
                },
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('coord.supervisors.index', compact('supervisors', 'department', 'programName'));
    }

    public function create()
    {
        $coordinator = Auth::user();
        $programName = optional($coordinator->coordinatorProfile?->program)->name;

        return view('coord.supervisors.create', compact('programName'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($request->email);

        if (User::whereRaw('LOWER(email) = ?', [$email])->where('role', 'supervisor')->exists()) {
            return back()->withErrors([
                'email' => 'This email is already associated with a supervisor account.',
            ])->withInput();
        }

        $registration = SupervisorRegistration::where('email', $email)->first();

        if ($registration) {
            $registration->update([
                'token' => SupervisorRegistration::generateToken(),
                'expires_at' => now()->addHours(24),
                'verified_at' => null,
            ]);
        } else {
            $registration = SupervisorRegistration::create([
                'email' => $email,
                'token' => SupervisorRegistration::generateToken(),
                'expires_at' => now()->addHours(24),
            ]);
        }

        try {
            Mail::to($email)->send(new SupervisorVerificationEmail($registration));
        } catch (\Exception $e) {
            Log::error('Coordinator supervisor invite failed: '.$e->getMessage());

            return back()->with('error', 'Failed to send the invitation email. Please try again.')->withInput();
        }

        return redirect()->route('coord.supervisors.index')
            ->with('success', 'Supervisor invitation sent successfully. The link will expire in 24 hours.');
    }
}
