<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinatorStudentController extends Controller
{
    /**
     * Get coordinator's department and program info
     */
    protected function getCoordinatorInfo(): array
    {
        $coordinator = Auth::user();
        $department = $coordinator->coordinatorProfile?->department;
        $program = $coordinator->coordinatorProfile?->program;
        $programName = $program?->name;

        return [
            'coordinator' => $coordinator,
            'department' => $department,
            'program' => $program,
            'programName' => $programName,
        ];
    }

    /**
     * Ensure student belongs to coordinator's department and program
     */
    protected function authorizeStudentAccess(User $student, ?string $programName = null, bool $requireProgram = true): void
    {
        $info = $this->getCoordinatorInfo();
        $department = $info['department'];
        $programName = $programName ?? $info['programName'];

        if (! $student->studentProfile ||
            $student->studentProfile->department !== $department ||
            ($requireProgram && ! empty($programName) && $student->studentProfile->course !== $programName)) {
            abort(403, 'Unauthorized access to student.');
        }
    }

    public function index(Request $request)
    {
        $info = $this->getCoordinatorInfo();
        $department = $info['department'];
        $programName = $info['programName'];

        // Base query for students in coordinator's department AND program
        $query = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($q) use ($department, $programName) {
                $q->where('department', $department)
                  ->where('course', $programName);
            })
            ->with([
                'studentProfile.company',
                'studentProfile.supervisor',
                'studentProfile.program',
            ])
            ->withCount(['attendanceLogs as pending_recoveries_count' => function ($q) {
                $q->where('is_recovered', true)
                  ->whereNull('recovery_approved');
            }])
            ->withSum(['attendanceLogs as total_minutes_worked' => function ($q) {
                $q->where('is_recovered', false)
                  ->orWhere('recovery_approved', true);
            }], 'minutes_worked')
            ->withCount(['documentSubmissions as pre_docs_count' => function ($q) {
                $q->select(\DB::raw('count(distinct(document_requirement_id))'))
                  ->whereHas('requirement', function ($rq) {
                      $rq->where('type', 'pre_placement');
                  });
            }])
            ->withCount(['documentSubmissions as post_docs_count' => function ($q) {
                $q->select(\DB::raw('count(distinct(document_requirement_id))'))
                  ->whereHas('requirement', function ($rq) {
                      $rq->where('type', 'post_placement');
                  });
            }]);

        // Apply filters (status, supervisor, search; program is fixed)
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->whereHas('studentProfile', function ($q) use ($status) {
                $q->where('ojt_status', $status);
            });
        }

        $supervisorFilter = $request->get('supervisor', 'all');
        if ($supervisorFilter === 'assigned') {
            $query->whereHas('studentProfile', function ($q) {
                $q->whereNotNull('supervisor_id');
            });
        } elseif ($supervisorFilter === 'pending') {
            $query->whereHas('studentProfile', function ($q) {
                $q->whereNull('supervisor_id');
            });
        }

        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('studentProfile', function ($subQ) use ($search) {
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

        // Get statistics for this specific program (optimized - single base query)
        $baseQuery = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($q) use ($department, $programName) {
                $q->where('department', $department)
                  ->where('course', $programName);
            });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->whereHas('studentProfile', function ($q) {
                $q->where('ojt_status', 'active');
            })->count(),
            'pending' => (clone $baseQuery)->whereHas('studentProfile', function ($q) {
                $q->where('ojt_status', 'pending');
            })->count(),
            'completed' => (clone $baseQuery)->whereHas('studentProfile', function ($q) {
                $q->where('ojt_status', 'completed');
            })->count(),
        ];

        $totalPreRequirements = \App\Models\DocumentRequirement::active()->prePlacement()->count();
        $totalPostRequirements = \App\Models\DocumentRequirement::active()->postPlacement()->count();

        // Add current filter/sort values for the view
        $students->appends($request->only(['status', 'search', 'sort', 'supervisor']));

        return view('coord.students.index', compact(
            'students', 
            'stats', 
            'status', 
            'search', 
            'sort', 
            'programName', 
            'supervisorFilter', 
            'totalPreRequirements',
            'totalPostRequirements'
        ));
    }

    /**
     * Show locator view for coordinator to see where students are assigned for OJT.
     *
     * Groups only the coordinator's own students by OJT site and allows opening each site in Google Maps.
     */
    public function locator()
    {
        $info = $this->getCoordinatorInfo();
        $department = $info['department'];
        $programName = $info['programName'];

        // Get ONLY this coordinator's students (same logic as index)
        $students = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($q) use ($department, $programName) {
                $q->where('department', $department)
                  ->where('course', $programName);
            })
            ->with([
                'studentProfile.supervisor.supervisorProfile.company',
                'studentProfile.company',
                'acceptanceLetters.company',
                'attendanceLogs' => function($q) {
                    $q->where('work_date', today());
                }
            ])
            ->get();

        // Group students by derived OJT site (company name + address)
        $sites = [];

        foreach ($students as $student) {
            $profile = $student->studentProfile;
            if (! $profile) {
                continue;
            }

            // Get latest acceptance with company (if any)
            $acceptance = $student->acceptanceLetters
                ? $student->acceptanceLetters->sortByDesc('start_date')->first()
                : null;

            // Derive company name/address using same priority as coordinator show page
            $companyName = $profile->company?->name
                ?? $acceptance?->company?->name
                ?? $profile->supervisor?->supervisorProfile?->company?->name;

            $companyAddress = $profile->company?->address
                ?? $acceptance?->company?->address
                ?? $profile->supervisor?->supervisorProfile?->company?->address;

            // If we truly have no site info, skip for locator
            if (! $companyName && ! $companyAddress) {
                continue;
            }

            $key = strtolower(trim(($companyName ?? 'Unknown Site').'|'.($companyAddress ?? 'Unknown Address')));

            if (! isset($sites[$key])) {
                $sites[$key] = [
                    'company_name' => $companyName ?? 'Unknown Site',
                    'company_address' => $companyAddress ?? 'Unknown Address',
                    'students' => [],
                ];
            }

            $sites[$key]['students'][] = $student;
        }

        return view('coord.students.locator', [
            'sites' => collect($sites)->values(),
            'programName' => $programName,
        ]);
    }

    public function show(User $student)
    {
        $info = $this->getCoordinatorInfo();
        $department = $info['department'];
        $programName = $info['programName'];

        // Ensure student belongs to coordinator's department AND (program if coordinator has one)
        $this->authorizeStudentAccess($student, $programName, false);

        // Load related data
        $student->load([
            'studentProfile.company',
            'studentProfile.supervisor',
            'studentProfile.supervisor.supervisorProfile.company',
            'studentProfile.program',
            'attendanceLogs' => function ($q) {
                $q->orderBy('work_date', 'desc')->limit(10);
            },
            'weeklyReports' => function ($q) {
                $q->orderBy('week_start_date', 'desc')->limit(10);
            },
            'monthlyEvaluations' => function ($q) {
                $q->orderBy('evaluation_year', 'desc')->orderBy('evaluation_month', 'desc');
            },
            'finalEvaluation',
        ]);

        $attendanceStats = [
            'total_days' => $student->attendanceLogs()->count(),
            'completed_days' => $student->attendanceLogs()->where(function($q) {
                $q->where(function($am) {
                    $am->whereNotNull('am_in_time')->whereNotNull('am_out_time');
                })->orWhere(function($pm) {
                    $pm->whereNotNull('pm_in_time')->whereNotNull('pm_out_time');
                })->orWhere('minutes_worked', '>', 0);
            })->count(),
            'missing_checkout' => $student->attendanceLogs()->where(function($q) {
                $q->where(function($am) {
                    $am->whereNotNull('am_in_time')->whereNull('am_out_time');
                })->orWhere(function($pm) {
                    $pm->whereNotNull('pm_in_time')->whereNull('pm_out_time');
                });
            })->count(),
        ];

        $reportStats = [
            'total_reports' => $student->weeklyReports()->count(),
            'this_week' => $student->weeklyReports()->whereBetween('week_start_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
        ];

        $acceptance = \App\Models\AcceptanceLetter::where('student_user_id', $student->id)->latest()->with('company')->first();

        $derivedCompanyName = $student->studentProfile?->company?->name
            ?? $acceptance?->company?->name
            ?? $student->studentProfile?->supervisor?->supervisorProfile?->company?->name;
        $derivedCompanyAddress = $student->studentProfile?->company?->address
            ?? $acceptance?->company?->address
            ?? $student->studentProfile?->supervisor?->supervisorProfile?->company?->address;

        $companySource = null;
        if ($student->studentProfile?->company) {
            $companySource = 'assigned';
        } elseif ($acceptance?->company) {
            $companySource = 'acceptance';
        } elseif ($student->studentProfile?->supervisor?->supervisorProfile?->company) {
            $companySource = 'supervisor';
        }

        // Get available companies for assignment
        $availableCompanies = Company::where('department', $info['department'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Eligible supervisors for assigned company
        $eligibleSupervisors = collect();
        $studentCompanyId = $student->studentProfile?->assigned_company_id;

        if ($studentCompanyId) {
            // For listed companies - show supervisors from that specific company
            $eligibleSupervisors = User::where('role', 'supervisor')
                ->whereHas('supervisorProfile', function ($q) use ($studentCompanyId) {
                    $q->where('company_id', $studentCompanyId);
                })
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('coord.students.show', compact(
            'student',
            'availableCompanies',
            'eligibleSupervisors',
            'studentCompanyId',
            'attendanceStats',
            'reportStats',
            'derivedCompanyName',
            'derivedCompanyAddress',
            'companySource',
            'acceptance'
        ));
    }

    public function updateCompany(Request $request, User $student)
    {
        $this->authorizeStudentAccess($student);

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
        $this->authorizeStudentAccess($student);

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
        $info = $this->getCoordinatorInfo();
        $coordinator = $info['coordinator'];

        $this->authorizeStudentAccess($student);

        $request->validate([
            'action' => ['required', 'in:assign_existing,create_from_proposal'],
            'supervisor_id' => ['required_if:action,assign_existing', 'exists:users,id'],
        ]);

        $action = $request->input('action');
        $supervisor = null;

        if ($action === 'create_from_proposal') {
            // This action is no longer supported - supervisor assignment requests table was removed
            return back()->withErrors(['error' => 'This action is no longer available. Please use the supervisor registration flow instead.']);
        } elseif ($action === 'assign_existing') {
            // Validate existing supervisor
            $supervisor = User::where('id', $request->supervisor_id)->where('role', 'supervisor')->firstOrFail();
            $studentCompanyId = $student->studentProfile?->assigned_company_id;
            $supervisorCompanyId = $supervisor->supervisorProfile?->company_id ?? null;

            if ($studentCompanyId) {
                // For listed companies - supervisor must be from same company
                abort_unless($studentCompanyId && $supervisorCompanyId && $studentCompanyId === $supervisorCompanyId, 422);
            } else {
                abort(422, 'No company assigned to student');
            }
        }

        // Assign supervisor to student
        $student->studentProfile->update([
            'supervisor_id' => $supervisor->id,
        ]);

        // Notifications
        \App\Models\Notification::create([
            'user_id' => $student->id,
            'type' => 'supervisor_assigned',
            'title' => 'Supervisor Assigned',
            'message' => 'Your coordinator assigned a supervisor to your OJT.',
            'data' => ['supervisor_id' => $supervisor->id],
        ]);

        \App\Models\Notification::create([
            'user_id' => $supervisor->id,
            'type' => 'student_assigned',
            'title' => 'Student Assigned',
            'message' => 'You have been assigned as supervisor for '.$student->name.'.',
            'data' => ['student_user_id' => $student->id],
        ]);

        // Messages to student and supervisor (humanized)
        \App\Models\Message::create([
            'sender_id' => $coordinator->id,
            'recipient_id' => $student->id,
            'subject' => 'Your OJT Supervisor has been assigned',
            'message' => 'Hi '.$student->name.",\n\nYour OJT supervisor has been assigned: ".$supervisor->name.' ('.$supervisor->email.").\n\nIf you have questions, feel free to reply here.\n\n— ".$coordinator->name,
        ]);

        \App\Models\Message::create([
            'sender_id' => $coordinator->id,
            'recipient_id' => $supervisor->id,
            'subject' => 'New student assignment: '.$student->name,
            'message' => 'Hi '.$supervisor->name.",\n\nYou have been assigned as supervisor for: ".$student->name.".\nPlease coordinate their onboarding and evaluations.\n\n— ".$coordinator->name,
        ]);

        return back()->with('success', 'Supervisor assigned successfully.');
    }

    public function notifyMoaReady(Request $request)
    {
        $info = $this->getCoordinatorInfo();
        $department = $info['department'];
        $programName = $info['programName'];

        // Validate company_id is provided
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        $companyId = $request->input('company_id');

        // Get company details
        $company = Company::findOrFail($companyId);

        // Get students in coordinator's program whose SUPERVISOR is from this company
        $students = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($q) use ($department, $programName, $companyId) {
                $q->where('department', $department)
                  ->where('course', $programName)
                  ->whereNotNull('supervisor_id') // Must have a supervisor
                  ->whereHas('supervisor.supervisorProfile', function ($supervisorQuery) use ($companyId) {
                      $supervisorQuery->where('company_id', $companyId); // Supervisor's company matches
                  });
            })
            ->get();

        if ($students->isEmpty()) {
            return back()->with('info', "No students found in {$programName} with supervisors from {$company->name}.");
        }

        $notifiedCount = 0;
        $alreadyNotifiedCount = 0;

        foreach ($students as $student) {
            // Check if student already has MOA Ready notification for this company
            $hasNotification = $student->notifications()
                ->where('type', 'App\\Notifications\\MoaReadyNotification')
                ->whereJsonContains('data->type', 'moa_ready')
                ->whereJsonContains('data->company_id', $companyId)
                ->exists();

            if (!$hasNotification) {
                $student->notify(new \App\Notifications\MoaReadyNotification($company));
                $notifiedCount++;
            } else {
                $alreadyNotifiedCount++;
            }
        }

        if ($notifiedCount === 0) {
            return back()->with('info', "All {$students->count()} student(s) with supervisors from {$company->name} have already been notified.");
        }

        $message = "Successfully notified {$notifiedCount} student(s) in {$programName} with supervisors from {$company->name}.";
        if ($alreadyNotifiedCount > 0) {
            $message .= " ({$alreadyNotifiedCount} already notified)";
        }

        return back()->with('success', $message);
    }
}
