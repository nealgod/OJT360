<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PlacementRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlacementRequestController extends Controller
{
    public function index()
    {
        $requests = Auth::user()->placementRequests()->with('company')->latest()->paginate(10);
        return view('placements.index', compact('requests'));
    }

    public function create()
    {
        $user = Auth::user();
        $department = $user->studentProfile?->department;
        $companies = Company::query()
            ->when($department, fn($q) => $q->where('department', $department))
            ->orderBy('name')
            ->get(['id','name','department']);
        return view('placements.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'external_company_name' => ['required_without:company_id', 'nullable', 'string', 'max:255'],
            'external_company_address' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $path = null;
        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('placement-proofs', 'public');
        }

        $companyId = $request->filled('company_id') ? (int) $request->input('company_id') : null;

        $placement = PlacementRequest::create([
            'student_user_id' => Auth::id(),
            'company_id' => $companyId,
            'external_company_name' => $request->string('external_company_name'),
            'external_company_address' => $request->string('external_company_address'),
            'start_date' => $request->date('start_date'),
            'contact_person' => $request->string('contact_person'),
            'note' => $request->string('note'),
            'proof_path' => $path,
        ]);

        // Notify coordinator (reuse existing Notification model)
        $coordinator = User::where('role', 'coordinator')
            ->whereHas('coordinatorProfile', function($q) use ($user) {
                $q->where('department', $user->studentProfile?->department);
            })
            ->first();

        if ($coordinator) {
            \App\Models\Notification::create([
                'user_id' => $coordinator->id,
                'type' => 'placement_request',
                'title' => 'New Placement Request',
                'message' => $user->name . ' submitted a placement request.' . ($placement->company_id ? '' : ' (External company)'),
                'data' => [
                    'placement_request_id' => $placement->id,
                    'student_user_id' => $user->id,
                    'company_id' => $placement->company_id,
                    'external_company_name' => $placement->external_company_name,
                ],
            ]);
        }

        return redirect()->route('placements.index')->with('success', 'Placement request submitted. Your coordinator will review it.');
    }

    // Coordinator actions
    public function inbox()
    {
        $user = Auth::user();
        $requests = PlacementRequest::with(['student','company'])
            ->whereHas('student.studentProfile', function($q) use ($user) {
                $q->where('department', $user->coordinatorProfile?->department);
            })
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
        return view('placements.inbox', compact('requests'));
    }

    public function approve(PlacementRequest $placementRequest, Request $request)
    {
        $this->authorizeAction($placementRequest);

        $request->validate([
            'start_date' => ['required', 'date'],
        ]);

        $placementRequest->update([
            'status' => 'approved',
            'start_date' => $request->date('start_date'),
            'decided_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        // Assign to student profile and activate OJT
        $student = $placementRequest->student;
        if ($student && $student->studentProfile) {
            $student->studentProfile->update([
                'assigned_company_id' => $placementRequest->company_id,
                'ojt_status' => 'active',
            ]);
        }

        // Notify student
        \App\Models\Notification::create([
            'user_id' => $student->id,
            'type' => 'placement_decision',
            'title' => 'Placement Approved',
            'message' => 'Your coordinator approved your placement. You may start your OJT.',
            'data' => [
                'placement_request_id' => $placementRequest->id,
            ],
        ]);

        return back()->with('success', 'Placement approved and OJT activated.');
    }

    public function decline(PlacementRequest $placementRequest, Request $request)
    {
        $this->authorizeAction($placementRequest);

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $placementRequest->update([
            'status' => 'declined',
            'decided_by' => Auth::id(),
            'decided_at' => now(),
            'note' => $placementRequest->note ? ($placementRequest->note . "\n\nDecline reason: " . $request->reason) : ('Decline reason: ' . $request->reason),
        ]);

        // Notify student
        \App\Models\Notification::create([
            'user_id' => $placementRequest->student_user_id,
            'type' => 'placement_decision',
            'title' => 'Placement Declined',
            'message' => 'Your placement request was declined. Reason: ' . $request->reason,
            'data' => [
                'placement_request_id' => $placementRequest->id,
            ],
        ]);

        return back()->with('success', 'Placement declined.');
    }

    private function authorizeAction(PlacementRequest $placementRequest): void
    {
        $user = Auth::user();
        abort_unless($user && $user->isCoordinator(), 403);
        abort_unless($user->coordinatorProfile && $user->coordinatorProfile->department === ($placementRequest->student->studentProfile?->department), 403);
    }
}


