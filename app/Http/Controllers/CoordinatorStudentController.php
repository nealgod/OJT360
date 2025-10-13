<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Ensure student belongs to coordinator's department AND program
        if (!$student->studentProfile || 
            $student->studentProfile->department !== $department || 
            $student->studentProfile->course !== $programName) {
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

        return view('coord.students.show', compact('student', 'availableCompanies'));
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
}