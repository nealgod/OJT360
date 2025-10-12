<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies for students to view.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isStudent()) {
            // Students see only companies assigned to their department
            $studentDepartment = $user->studentProfile->department ?? null;
            $companies = Company::where('status', 'active')
                ->where('department', $studentDepartment)
                ->orderBy('name')
                ->get();
        } elseif ($user->isCoordinator()) {
            // Coordinators see all active companies in their department, plus those they own
            $coordDept = $user->coordinatorProfile?->department;
            $companies = Company::where('status', 'active')
                ->where(function($q) use ($user, $coordDept) {
                    $q->where('coordinator_id', $user->id)
                      ->orWhere('department', $coordDept);
                })
                ->orderBy('name')
                ->get();
        } else {
            // Admin and supervisors see all companies
            $companies = Company::where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        return view('companies.index', compact('companies'));
    }


    /**
     * Show create form for coordinators.
     */
    public function create()
    {
        $user = auth()->user();
        abort_unless($user && $user->isCoordinator(), 403);

        $department = $user->coordinatorProfile?->department;
        return view('companies.create', compact('department'));
    }

    /**
     * Store a new company (coordinator-owned).
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $user->isCoordinator(), 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Company::create([
            'name' => $request->string('name'),
            'department' => $user->coordinatorProfile?->department,
            'coordinator_id' => $user->id,
            'address' => $request->string('address'),
            'contact_person' => $request->string('contact_person'),
            'contact_email' => $request->string('contact_email'),
            'contact_phone' => $request->string('contact_phone'),
            'status' => $request->string('status', 'active'),
        ]);

        return redirect()->route('companies.index')->with('success', 'Company added successfully.');
    }

    /**
     * Edit company (only by its coordinator or admin).
     */
    public function edit(Company $company)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || ($user->isCoordinator() && $company->coordinator_id === $user->id)), 403);

        $department = $company->department;
        return view('companies.edit', compact('company', 'department'));
    }

    /**
     * Update company details.
     */
    public function update(Request $request, Company $company)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || ($user->isCoordinator() && $company->coordinator_id === $user->id)), 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name,' . $company->id],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $company->update([
            'name' => $request->string('name'),
            // keep department fixed to creator's department
            'address' => $request->string('address'),
            'contact_person' => $request->string('contact_person'),
            'contact_email' => $request->string('contact_email'),
            'contact_phone' => $request->string('contact_phone'),
            'status' => $request->string('status', $company->status),
        ]);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    /**
     * Delete company.
     */
    public function destroy(Company $company)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || ($user->isCoordinator() && $company->coordinator_id === $user->id)), 403);

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
