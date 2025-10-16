<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\SupervisorAssignmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CoordinatorStudentController extends Controller
{
    public function index(Request $request)
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        $program = $coordinator->coordinatorProfile?->program;
        $programName = $program?->name; // Get the program name string
        
        // Base query for students in coordinator's department AND program
        $query = User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($department, $programName) {
                $q->where('department', $department)
                  ->where('course', $programName);
            })
            ->with(['studentProfile.company', 'studentProfile.supervisor']);

        // Apply filters (only status and search since program is fixed)
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->whereHas('studentProfile', function($q) use ($status) {
                $q->where('ojt_status', $status);
            });
        }

        $search = $request->get('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('studentProfile', function($subQ) use ($search) {
                      $subQ->where('student_id', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        $sort = $request->get('sort', 'name');
        switch ($sort) {
            case 'id':
                $query->join('student_profiles', 'users.id', '=', 'student_profiles.user_id')
                      ->orderBy('student_profiles.student_id')
                      ->select('users.*');
                break;
            case 'status':
                $query->join('student_profiles', 'users.id', '=', 'student_profiles.user_id')
                      ->orderBy('student_profiles.ojt_status')
                      ->select('users.*');
                break;
            default: // name
                $query->orderBy('name');
                break;
        }

        $students = $query->paginate(15);

        // Get statistics for this specific program
        $stats = [
            'total' => User::where('role', 'intern')
                ->whereHas('studentProfile', function($q) use ($department, $programName) {
                    $q->where('department', $department)
                      ->where('course', $programName);
                })->count(),
            'active' => User::where('role', 'intern')
                ->whereHas('studentProfile', function($q) use ($department, $programName) {
                    $q->where('department', $department)
                      ->where('course', $programName)
                      ->where('ojt_status', 'active');
                })->count(),
            'pending' => User::where('role', 'intern')
                ->whereHas('studentProfile', function($q) use ($department, $programName) {
                    $q->where('department', $department)
                      ->where('course', $programName)
                      ->where('ojt_status', 'pending');
                })->count(),
            'completed' => User::where('role', 'intern')
                ->whereHas('studentProfile', function($q) use ($department, $programName) {
                    $q->where('department', $department)
                      ->where('course', $programName)
                      ->where('ojt_status', 'completed');
                })->count(),
        ];

        // Add current filter/sort values for the view
        $students->appends($request->only(['status', 'search', 'sort']));

        return view('coord.students.index', compact('students', 'stats', 'status', 'search', 'sort', 'programName'));
    }

    public function show(User $student)
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        $program = $coordinator->coordinatorProfile?->program;
        $programName = $program?->name;
        
        // Ensure student belongs to coordinator's department AND (program if coordinator has one)
        if (!$student->studentProfile || 
            $student->studentProfile->department !== $department || 
            (!empty($programName) && $student->studentProfile->course !== $programName)) {
            abort(403, 'Unauthorized access to student.');
        }

        // Load related data
        $student->load([
            'studentProfile.company',
            'studentProfile.supervisor',
            'placementRequests' => function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'attendanceLogs' => function($q) {
                $q->orderBy('work_date', 'desc')->limit(10);
            },
            'dailyReports' => function($q) {
                $q->orderBy('work_date', 'desc')->limit(10);
            }
        ]);

        // Get available companies for assignment
        $availableCompanies = Company::where('department', $department)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Eligible supervisors for assigned company
        $eligibleSupervisors = collect();
        $studentCompanyId = $student->studentProfile?->assigned_company_id;
        if ($studentCompanyId) {
            $eligibleSupervisors = User::where('role', 'supervisor')
                ->whereHas('supervisorProfile', function($q) use ($studentCompanyId) {
                    $q->where('company_id', $studentCompanyId);
                })
                ->orderBy('name')
                ->get(['id','name']);
        }

        // Latest proposal, guarded if table not yet migrated
        $latestProposal = null;
        if (Schema::hasTable('supervisor_assignment_requests')) {
            $latestProposal = SupervisorAssignmentRequest::where('student_user_id', $student->id)
                ->latest()
                ->first();
        }

        return view('coord.students.show', compact('student', 'availableCompanies', 'eligibleSupervisors', 'latestProposal', 'studentCompanyId'));
    }

    public function updateCompany(Request $request, User $student)
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        $program = $coordinator->coordinatorProfile?->program;
        $programName = $program?->name;
        
        // Ensure student belongs to coordinator's department AND program
        if (!$student->studentProfile || 
            $student->studentProfile->department !== $department || 
            $student->studentProfile->course !== $programName) {
            abort(403, 'Unauthorized access to student.');
        }

        $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'required_hours' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $student->studentProfile->update([
            'assigned_company_id' => $request->company_id,
            'required_hours' => $request->required_hours,
        ]);

        return back()->with('success', 'Student assignment updated successfully.');
    }

    public function updateStatus(Request $request, User $student)
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        $program = $coordinator->coordinatorProfile?->program;
        $programName = $program?->name;
        
        // Ensure student belongs to coordinator's department AND program
        if (!$student->studentProfile || 
            $student->studentProfile->department !== $department || 
            $student->studentProfile->course !== $programName) {
            abort(403, 'Unauthorized access to student.');
        }

        $request->validate([
            'ojt_status' => ['required', 'in:pending,active,completed'],
        ]);

        $student->studentProfile->update([
            'ojt_status' => $request->ojt_status,
        ]);

        return back()->with('success', 'Student status updated successfully.');
    }

    public function assignSupervisor(Request $request, User $student)
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        $program = $coordinator->coordinatorProfile?->program;
        $programName = $program?->name;

        if (!$student->studentProfile || 
            $student->studentProfile->department !== $department || 
            $student->studentProfile->course !== $programName) {
            abort(403, 'Unauthorized access to student.');
        }

        $request->validate([
            'supervisor_id' => ['required', 'exists:users,id'],
        ]);

        // Ensure supervisor belongs to same company
        $supervisor = User::where('id', $request->supervisor_id)->where('role', 'supervisor')->firstOrFail();
        $studentCompanyId = $student->studentProfile?->assigned_company_id;
        $supervisorCompanyId = $supervisor->supervisorProfile?->company_id ?? null;
        abort_unless($studentCompanyId && $supervisorCompanyId && $studentCompanyId === $supervisorCompanyId, 422);

        $student->studentProfile->update([
            'supervisor_id' => $supervisor->id,
        ]);

        // Notifications
        \App\Models\Notification::create([
            'user_id' => $student->id,
            'type' => 'supervisor_assigned',
            'title' => 'Supervisor Assigned',
            'message' => 'Your coordinator assigned a supervisor to your OJT.',
            'data' => [ 'supervisor_id' => $supervisor->id ],
        ]);

        \App\Models\Notification::create([
            'user_id' => $supervisor->id,
            'type' => 'student_assigned',
            'title' => 'Student Assigned',
            'message' => 'You have been assigned as supervisor for ' . $student->name . '.',
            'data' => [ 'student_user_id' => $student->id ],
        ]);

        // Messages to student and supervisor (humanized)
        \App\Models\Message::create([
            'sender_id' => $coordinator->id,
            'recipient_id' => $student->id,
            'subject' => 'Your OJT Supervisor has been assigned',
            'message' => 'Hi ' . $student->name . ",\n\nYour OJT supervisor has been assigned: " . $supervisor->name . " (" . $supervisor->email . ").\n\nIf you have questions, feel free to reply here.\n\n— " . $coordinator->name,
        ]);

        \App\Models\Message::create([
            'sender_id' => $coordinator->id,
            'recipient_id' => $supervisor->id,
            'subject' => 'New student assignment: ' . $student->name,
            'message' => 'Hi ' . $supervisor->name . ",\n\nYou have been assigned as supervisor for: " . $student->name . ".\nPlease coordinate their onboarding and evaluations.\n\n— " . $coordinator->name,
        ]);

        return back()->with('success', 'Supervisor assigned successfully.');
    }
}