<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CoordinatorSupervisorController extends Controller
{
    public function index()
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        
        // Get all supervisors that have students from this coordinator's department
        $supervisors = User::where('role', 'supervisor')
            ->whereHas('studentProfiles', function($query) use ($department) {
                $query->where('department', $department);
            })
            ->with(['supervisorProfile.company', 'studentProfiles.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('coord.supervisors.index', compact('supervisors'));
    }

    public function create()
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        
        // Get companies from the coordinator's department
        $companies = \App\Models\Company::where('department', $department)
            ->where('status', 'active')
            ->get();
            
        return view('coord.supervisors.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'company_id' => ['required', 'exists:companies,id'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        // Create supervisor user
        $supervisor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'supervisor',
            'password' => bcrypt(Str::random(12)), // Temporary password
            'email_verified_at' => now(),
        ]);

        // Create supervisor profile
        $supervisor->supervisorProfile()->create([
            'company_id' => $request->company_id,
            'employee_id' => $request->employee_id ?? 'SUP-' . $supervisor->id,
            'position' => $request->position,
            'phone' => $request->phone,
        ]);

        return redirect()->route('coord.supervisors.index')
            ->with('success', 'Supervisor account created successfully.');
    }

}