<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Department;
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
            // Coordinators see ALL companies in their department (active and inactive)
            $coordDept = $user->coordinatorProfile?->department;
            $companies = Company::where(function ($q) use ($user, $coordDept) {
                $q->where('coordinator_id', $user->id)
                  ->orWhere('department', $coordDept);
            })
                ->orderBy('status', 'desc') // Active first, then inactive
                ->orderBy('name')
                ->get();
        } else {
            // Admin and supervisors see all companies (active and inactive)
            $companies = Company::orderBy('status', 'desc') // Active first, then inactive
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
        abort_unless($user && ($user->isCoordinator() || $user->isAdmin()), 403);

        $department = $user->isCoordinator() ? $user->coordinatorProfile?->department : null;
        $departments = $user->isAdmin() ? Department::orderBy('name')->pluck('name')->toArray() : [];

        return view('companies.create', compact('department', 'departments'));
    }

    /**
     * Store a new company (coordinator-owned).
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isCoordinator() || $user->isAdmin()), 403);

        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ];
        if ($user->isAdmin()) {
            $rules['department'] = ['required', 'string'];
        }
        $request->validate($rules);

        $company = Company::create([
            'name' => $request->string('name'),
            'department' => $user->isAdmin() ? $request->string('department') : $user->coordinatorProfile?->department,
            'coordinator_id' => $user->isAdmin() ? null : $user->id,
            'address' => $request->string('address'),
            'contact_person' => $request->string('contact_person'),
            'contact_email' => $request->string('contact_email'),
            'contact_phone' => $request->string('contact_phone'),
            'status' => $request->string('status', 'active'),
        ]);

        AuditLog::log('created', 'Company created', 'Company', $company->id, null, $company->toArray());

        return redirect()->route('companies.index')->with('success', 'Company added successfully.');
    }

    /**
     * Edit company (only by its coordinator or admin).
     */
    public function edit(Company $company)
    {
        $user = auth()->user();
        $coordDept = $user->coordinatorProfile?->department;

        abort_unless($user && (
            $user->isAdmin() ||
            ($user->isCoordinator() && $company->coordinator_id === $user->id) ||
            ($user->isCoordinator() && $company->department === $coordDept)
        ), 403);

        $department = $company->department;
        $departments = $user->isAdmin() ? Department::orderBy('name')->pluck('name')->toArray() : [];

        return view('companies.edit', compact('company', 'department', 'departments'));
    }

    /**
     * Update company details.
     */
    public function update(Request $request, Company $company)
    {
        $user = auth()->user();
        $coordDept = $user->coordinatorProfile?->department;

        abort_unless($user && (
            $user->isAdmin() ||
            ($user->isCoordinator() && $company->coordinator_id === $user->id) ||
            ($user->isCoordinator() && $company->department === $coordDept)
        ), 403);

        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:companies,name,'.$company->id],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ];
        if ($user->isAdmin()) {
            $rules['department'] = ['required', 'string'];
        }
        $request->validate($rules);

        $old = $company->getOriginal();
        $company->update([
            'name' => $request->string('name'),
            'department' => $user->isAdmin() ? $request->string('department') : $company->department,
            'address' => $request->string('address'),
            'contact_person' => $request->string('contact_person'),
            'contact_email' => $request->string('contact_email'),
            'contact_phone' => $request->string('contact_phone'),
            'status' => $request->string('status', $company->status),
        ]);

        AuditLog::log('updated', 'Company updated', 'Company', $company->id, $old, $company->toArray());

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    /**
     * Toggle company status (active/inactive).
     */
    public function toggleStatus(Company $company)
    {
        $user = auth()->user();
        $coordDept = $user->coordinatorProfile?->department;

        abort_unless($user && (
            $user->isAdmin() ||
            ($user->isCoordinator() && $company->coordinator_id === $user->id) ||
            ($user->isCoordinator() && $company->department === $coordDept)
        ), 403);

        $old = $company->getOriginal();
        $company->update([
            'status' => $company->status === 'active' ? 'inactive' : 'active',
        ]);

        $status = $company->status === 'active' ? 'activated' : 'deactivated';
        AuditLog::log('updated', 'Company status toggled', 'Company', $company->id, $old, $company->toArray());

        return redirect()->route('companies.index')->with('success', "Company {$status} successfully.");
    }

    /**
     * Delete company.
     */
    public function destroy(Company $company)
    {
        $user = auth()->user();
        $coordDept = $user->coordinatorProfile?->department;

        abort_unless($user && (
            $user->isAdmin() ||
            ($user->isCoordinator() && $company->coordinator_id === $user->id) ||
            ($user->isCoordinator() && $company->department === $coordDept)
        ), 403);

        $companyId = $company->id;
        $old = $company->toArray();
        $company->delete();

        AuditLog::log('deleted', 'Company deleted', 'Company', $companyId, $old, null);

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
