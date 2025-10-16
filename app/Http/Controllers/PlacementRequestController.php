<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PlacementRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlacementRequestController extends Controller
{
    public function index()
    {
        $requests = Auth::user()->placementRequests()
            ->whereNull('dismissed_at')
            ->with('company')
            ->latest()
            ->paginate(10);
        return view('placements.index', compact('requests'));
    }

    public function create()
    {
        $user = Auth::user();
        $department = $user->studentProfile?->department;
        $companies = Company::query()
            ->where('status', 'active')
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
            'external_company_name' => ['nullable', 'string', 'max:255'],
            'external_company_address' => ['nullable', 'string', 'max:255'],
            'position_title' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'shift_start' => ['nullable', 'date_format:H:i'],
            'shift_end' => ['nullable', 'date_format:H:i'],
            'working_days' => ['nullable', 'array'],
            'working_days.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'break_minutes' => ['nullable', 'integer', 'min:0', 'max:240'],
            'contact_person' => ['required', 'string', 'max:255'],
            'supervisor_name' => ['nullable', 'string', 'max:255'],
            'supervisor_email' => ['nullable', 'email', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        // Custom validation: Either company_id or external_company_name must be provided
        $hasCompany = $request->input('company_id') && $request->input('company_id') !== '';
        $hasExternalCompany = $request->filled('external_company_name');
        
        if (!$hasCompany && !$hasExternalCompany) {
            return back()->withErrors(['company_id' => 'Please either select a company or enter an external company name.'])->withInput();
        }

        // If external company is selected, external company name and address are required
        if (!$hasCompany) {
            if (!$request->filled('external_company_name')) {
                return back()->withErrors(['external_company_name' => 'External company name is required when no company is selected.'])->withInput();
            }
            if (!$request->filled('external_company_address')) {
                return back()->withErrors(['external_company_address' => 'External company address is required when no company is selected.'])->withInput();
            }
        }

        $path = null;
        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('placement-proofs', 'public');
        }

        $companyId = $hasCompany ? (int) $request->input('company_id') : null;

        $placement = PlacementRequest::create([
            'student_user_id' => Auth::id(),
            'company_id' => $companyId,
            'external_company_name' => $request->string('external_company_name'),
            'external_company_address' => $request->string('external_company_address'),
            'position_title' => $request->string('position_title'),
            'start_date' => $request->date('start_date'),
            'shift_start' => $request->input('shift_start'),
            'shift_end' => $request->input('shift_end'),
            'break_minutes' => $request->input('break_minutes', 60),
            'working_days' => $request->filled('working_days') ? array_values($request->input('working_days')) : null,
            'contact_person' => $request->string('contact_person'),
            'supervisor_name' => $request->string('supervisor_name'),
            'supervisor_email' => $request->string('supervisor_email'),
            'note' => $request->string('note'),
            'proof_path' => $path,
        ]);

        // Supervisor assignment will be done manually by coordinator later

        // Auto-create document submission for Letter of Acceptance if proof was uploaded
        if ($path && \Illuminate\Support\Facades\Schema::hasTable('document_requirements') && \Illuminate\Support\Facades\Schema::hasTable('student_document_submissions')) {
            $letterOfAcceptanceRequirement = \App\Models\DocumentRequirement::where('name', 'LIKE', '%Letter of Acceptance%')
                ->where('type', 'pre_placement')
                ->first();
            
            if ($letterOfAcceptanceRequirement) {
                \App\Models\StudentDocumentSubmission::create([
                    'student_user_id' => Auth::id(),
                    'document_requirement_id' => $letterOfAcceptanceRequirement->id,
                    'file_path' => $path,
                    'original_filename' => $request->file('proof')->getClientOriginalName(),
                    'file_size' => $request->file('proof')->getSize(),
                    'mime_type' => $request->file('proof')->getMimeType(),
                    'status' => 'submitted',
                ]);
            }
        }

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

            // Also create a message from student to coordinator
            \App\Models\Message::create([
                'sender_id' => $user->id,
                'recipient_id' => $coordinator->id,
                'subject' => 'New Placement Request - ' . ($placement->company?->name ?? $placement->external_company_name),
                'message' => "Hello " . $coordinator->name . ",\n\nI have submitted a placement request for " . ($placement->company?->name ?? $placement->external_company_name) . " starting on " . $placement->start_date->format('M d, Y') . ".\n\n" . 
                           "Company Details:\n" .
                           ($placement->company_id ? "• Company: " . $placement->company->name . "\n" : "• External Company: " . $placement->external_company_name . "\n") .
                           "• Contact Person: " . $placement->contact_person . "\n" .
                           ($placement->supervisor_name ? "• Supervisor: " . $placement->supervisor_name . "\n" : "") .
                           ($placement->supervisor_email ? "• Supervisor Email: " . $placement->supervisor_email . "\n" : "") .
                           ($placement->note ? "• Additional Notes: " . $placement->note . "\n" : "") .
                           "\nPlease review and approve my placement request. Thank you!",
            ]);
        }

        return redirect()->route('placements.index')->with('success', 'Placement request submitted. Your coordinator will review it.');
    }

    // Coordinator actions
    public function inbox(Request $request)
    {
        $user = Auth::user();
        $query = PlacementRequest::with(['student','company'])
            ->whereHas('student.studentProfile', function($q) use ($user) {
                $q->where('department', $user->coordinatorProfile?->department);
                $programName = optional($user->coordinatorProfile?->program)->name;
                if (!empty($programName)) {
                    $q->where('course', $programName);
                }
            })
            ->where('placement_requests.status', 'pending')
            ->whereNull('placement_requests.dismissed_at');

        // Apply filters
        $filter = $request->get('filter', 'all');
        switch ($filter) {
            case 'recent':
                $query->where('placement_requests.created_at', '>=', now()->subDays(7));
                break;
            case 'company':
                $query->whereNotNull('placement_requests.company_id');
                break;
            case 'external':
                $query->whereNull('placement_requests.company_id');
                break;
        }

        // Apply sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('placement_requests.created_at', 'asc');
                break;
            case 'name':
                $query->join('users', 'placement_requests.student_user_id', '=', 'users.id')
                      ->orderBy('users.name')
                      ->select('placement_requests.*');
                break;
            case 'company':
                $query->leftJoin('companies', 'placement_requests.company_id', '=', 'companies.id')
                      ->orderBy('companies.name')
                      ->select('placement_requests.*');
                break;
            default: // newest
                $query->orderBy('placement_requests.created_at', 'desc');
                break;
        }

        $requests = $query->paginate(10);
        
        // Add current filter/sort values for the view
        $requests->appends($request->only(['filter', 'sort']));
        
        return view('placements.inbox', compact('requests', 'filter', 'sort'));
    }

    public function approve(PlacementRequest $placementRequest, Request $request)
    {
        $this->authorizeAction($placementRequest);

        $request->validate([
            'start_date' => ['required', 'date'],
            'break_minutes' => ['nullable', 'integer', 'min:0', 'max:240'],
        ]);

        $placementRequest->update([
            'status' => 'approved',
            'start_date' => $request->date('start_date'),
            'break_minutes' => $request->input('break_minutes', $placementRequest->break_minutes),
            'decided_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        // Auto-void all other pending placement requests for this student
        $student = $placementRequest->student;
        $voidedCount = PlacementRequest::where('student_user_id', $student->id)
            ->where('id', '!=', $placementRequest->id)
            ->where('status', 'pending')
            ->count();
            
        PlacementRequest::where('student_user_id', $student->id)
            ->where('id', '!=', $placementRequest->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'voided',
                'decided_by' => Auth::id(),
                'decided_at' => now(),
            ]);

        // Notify student about voided requests if any
        if ($voidedCount > 0) {
            \App\Models\Notification::create([
                'user_id' => $student->id,
                'type' => 'placement_voided',
                'title' => 'Other Placement Requests Voided',
                'message' => "Your other {$voidedCount} pending placement request(s) have been automatically voided since one was approved.",
                'data' => [
                    'voided_count' => $voidedCount,
                ],
            ]);
        }

        // Assign to student profile and activate OJT
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

        // Also create a message from coordinator to student
        \App\Models\Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $student->id,
            'subject' => 'Placement Request Approved - ' . ($placementRequest->company?->name ?? $placementRequest->external_company_name),
            'message' => "Congratulations! Your placement request has been approved. You can now start your OJT at " . ($placementRequest->company?->name ?? $placementRequest->external_company_name) . " starting " . $placementRequest->start_date->format('M d, Y') . ". Please ensure you complete all required hours and submit your daily reports.",
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

        // Also create a message from coordinator to student
        \App\Models\Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $placementRequest->student_user_id,
            'subject' => 'Placement Request Declined - ' . ($placementRequest->company?->name ?? $placementRequest->external_company_name),
            'message' => "Your placement request has been declined. Reason: " . $request->reason . "\n\nYou may submit a new placement request with a different company or address the concerns mentioned above. Please feel free to contact me if you have any questions.",
        ]);

        return back()->with('success', 'Placement declined.');
    }

    public function dismiss(PlacementRequest $placementRequest)
    {
        // Only the student who owns the request can dismiss it
        abort_unless($placementRequest->student_user_id === Auth::id(), 403);
        
        $placementRequest->update(['dismissed_at' => now()]);
        
        return response()->json(['success' => true]);
    }

    public function myPlacement()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403);

        $placement = $user->placementRequests()
            ->where('status', 'approved')
            ->latest('decided_at')
            ->with('company')
            ->first();

        // Check if student already has an assigned supervisor
        $hasAssignedSupervisor = $user->studentProfile && $user->studentProfile->supervisor_id;
        $assignedSupervisor = null;
        
        if ($hasAssignedSupervisor) {
            $assignedSupervisor = $user->studentProfile->supervisor;
        }

        return view('placements.my', compact('placement', 'hasAssignedSupervisor', 'assignedSupervisor'));
    }

    public function proposeSupervisor(PlacementRequest $placementRequest, Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->id === $placementRequest->student_user_id, 403);
        abort_unless($placementRequest->status === 'approved', 400);

        $request->validate([
            'proposed_name' => ['nullable', 'string', 'max:255'],
            'proposed_email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Guard if table doesn't exist yet
        $proposal = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_assignment_requests')) {
            $proposal = \App\Models\SupervisorAssignmentRequest::create([
                'student_user_id' => $user->id,
                'company_id' => $placementRequest->company_id ?? $placementRequest->student->studentProfile?->assigned_company_id,
                'proposed_name' => (string) $request->input('proposed_name'),
                'proposed_email' => (string) $request->input('proposed_email'),
                'notes' => (string) $request->input('notes'),
            ]);
        }

        // Notify coordinator
        $coordinator = User::where('role', 'coordinator')
            ->whereHas('coordinatorProfile', function($q) use ($user) {
                $q->where('department', $user->studentProfile?->department);
            })
            ->first();

        if ($coordinator) {
            \App\Models\Notification::create([
                'user_id' => $coordinator->id,
                'type' => 'supervisor_proposal',
                'title' => 'Supervisor Details Submitted',
                'message' => $user->name . ' submitted supervisor details for their placement.',
                'data' => [
                    'proposal_id' => $proposal?->id,
                    'student_user_id' => $user->id,
                    'company_id' => $proposal->company_id ?? ($placementRequest->company_id ?? $placementRequest->student->studentProfile?->assigned_company_id),
                ],
            ]);

            // Also send a message to the coordinator's inbox for easy tracking
            $companyName = $placementRequest->company?->name ?? $placementRequest->external_company_name ?? 'the company';
            \App\Models\Message::create([
                'sender_id' => $user->id,
                'recipient_id' => $coordinator->id,
                'subject' => 'Supervisor details for my placement at ' . $companyName,
                'message' => "Hi " . $coordinator->name . ",\n\n" .
                    "I’d like to share my supervisor details for my approved OJT placement at " . $companyName . "." . "\n\n" .
                    "Supervisor Name: " . ($request->input('proposed_name') ?: '—') . "\n" .
                    "Supervisor Email: " . ($request->input('proposed_email') ?: '—') . "\n" .
                    (trim((string)$request->input('notes')) !== '' ? ("Notes: " . trim((string)$request->input('notes')) . "\n\n") : "") .
                    "Could you please create or assign this supervisor to my profile when you’re able?\n\n" .
                    "Thank you,\n" . $user->name,
            ]);
        }

        return back()->with('success', 'Supervisor details sent. Your coordinator will create or assign the supervisor for your company.');
    }

    private function authorizeAction(PlacementRequest $placementRequest): void
    {
        $user = Auth::user();
        abort_unless($user && $user->isCoordinator(), 403);
        $studentProfile = $placementRequest->student->studentProfile;
        $coordinatorProfile = $user->coordinatorProfile;
        abort_unless($coordinatorProfile && $studentProfile, 403);
        $sameDepartment = $coordinatorProfile->department === $studentProfile->department;
        $coordinatorProgramName = optional($coordinatorProfile->program)->name;
        $sameProgram = empty($coordinatorProgramName) ? true : ($coordinatorProgramName === $studentProfile->course);
        abort_unless($sameDepartment && $sameProgram, 403);
    }
}


