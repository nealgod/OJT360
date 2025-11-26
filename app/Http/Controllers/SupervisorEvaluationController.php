<?php

namespace App\Http\Controllers;

use App\Models\MonthlyEvaluation;
use App\Models\User;
use App\Models\AcceptanceLetter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class SupervisorEvaluationController extends Controller
{
    public function index()
    {
        $evaluations = MonthlyEvaluation::forSupervisor(Auth::id())
            ->with('student')
            ->orderByDesc('evaluation_year')
            ->orderByDesc('evaluation_month')
            ->paginate(15);

        return view('supervisor.evaluations.index', compact('evaluations'));
    }

    public function create(User $student)
    {
        // Verify student has a profile
        if (!$student->studentProfile) {
            return redirect()->back()
                ->with('error', 'Student profile not found.');
        }

        // Verify supervisor has access to this student
        if ($student->studentProfile->supervisor_id !== Auth::id()) {
            abort(403, 'You are not authorized to evaluate this student.');
        }

        // Get acceptance letter for internship period
        $acceptance = AcceptanceLetter::where('student_user_id', $student->id)
            ->orderByDesc('start_date')
            ->first();

        if (!$acceptance || !$acceptance->start_date) {
            return redirect()->back()
                ->with('error', 'Student must have an acceptance letter with a start date.');
        }

        // Default to current month and year (but allow selection)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Calculate internship period for validation
        $internshipStart = Carbon::parse($acceptance->start_date)->startOfMonth();
        $internshipEnd = $acceptance->end_date 
            ? Carbon::parse($acceptance->end_date)->endOfMonth() 
            : now()->addMonths(12)->endOfMonth(); // Default to 12 months if no end date

        // Calculate month number for current month (default)
        $currentMonthStart = now()->startOfMonth();
        $monthNumber = $internshipStart->diffInMonths($currentMonthStart) + 1;

        // Get company info (may be null)
        $company = $student->studentProfile->company;

        return view('supervisor.evaluations.create', compact(
            'student',
            'acceptance',
            'company',
            'currentMonth',
            'currentYear',
            'monthNumber',
            'internshipStart',
            'internshipEnd'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'evaluation_month' => 'required|integer|min:1|max:12',
            'evaluation_year' => 'required|integer|min:2020|max:' . (now()->year + 1),
            'work_assignment' => 'required|string|max:500',
            'rating_row_1' => 'required|integer|min:1|max:5',
            'rating_row_2' => 'required|integer|min:1|max:5',
            'rating_row_3' => 'required|integer|min:1|max:5',
            'rating_row_4' => 'required|integer|min:1|max:5',
            'rating_row_5' => 'required|integer|min:1|max:5',
            'rating_row_6' => 'required|integer|min:1|max:5',
            'rating_row_7' => 'required|integer|min:1|max:5',
            'rating_row_8' => 'required|integer|min:1|max:5',
            'rating_row_9' => 'required|integer|min:1|max:5',
            'rating_row_10' => 'required|integer|min:1|max:5',
            'rating_row_11' => 'required|integer|min:1|max:5',
            'rating_row_12' => 'required|integer|min:1|max:5',
            'rating_row_13' => 'required|integer|min:1|max:5',
            'rating_row_14' => 'required|integer|min:1|max:5',
            'rating_row_15' => 'required|integer|min:1|max:5',
            'rating_row_16' => 'required|integer|min:1|max:5',
            'rating_row_17' => 'required|integer|min:1|max:5',
            'rating_row_18' => 'required|integer|min:1|max:5',
            'rating_row_19' => 'required|integer|min:1|max:5',
            'rating_row_20' => 'required|integer|min:1|max:5',
            'comments_recommendations' => 'nullable|string|max:400',
        ]);

        // Get student info
        $student = User::findOrFail($validated['student_user_id']);
        $studentProfile = $student->studentProfile;
        $acceptance = AcceptanceLetter::where('student_user_id', $student->id)
            ->with('company')
            ->orderByDesc('start_date')
            ->first();
        $company = $acceptance ? $acceptance->company : null;

        // Validate selected month/year is within internship period
        $internshipStart = Carbon::parse($acceptance->start_date)->startOfMonth();
        $internshipEnd = $acceptance->end_date 
            ? Carbon::parse($acceptance->end_date)->endOfMonth() 
            : now()->addMonths(12)->endOfMonth();
        
        $selectedDate = Carbon::create($validated['evaluation_year'], $validated['evaluation_month'], 1);
        
        if ($selectedDate->lt($internshipStart) || $selectedDate->gt($internshipEnd)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['evaluation_month' => 'Selected month must be within the internship period.']);
        }

        // Check if evaluation already exists for selected month/year
        $existing = MonthlyEvaluation::where('student_user_id', $student->id)
            ->where('evaluation_year', $validated['evaluation_year'])
            ->where('evaluation_month', $validated['evaluation_month'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['evaluation_month' => 'An evaluation for ' . date('F Y', mktime(0, 0, 0, $validated['evaluation_month'], 1, $validated['evaluation_year'])) . ' already exists for this student.']);
        }

        // Calculate month number based on selected month/year
        $selectedMonthStart = $selectedDate->startOfMonth();
        $monthNumber = $internshipStart->diffInMonths($selectedMonthStart) + 1;
        
        // Ensure month_number is at least 1
        if ($monthNumber < 1) {
            $monthNumber = 1;
        }

        // Get coordinator
        $coordinatorId = null;
        if ($studentProfile && $studentProfile->program_id) {
            $coordinator = User::where('role', 'coordinator')
                ->whereHas('coordinatorProfile', function($q) use ($studentProfile) {
                    $q->where('program_id', $studentProfile->program_id);
                })
                ->first();
            $coordinatorId = $coordinator?->id;
        }

        // Create evaluation
        $evaluation = MonthlyEvaluation::create([
            'student_user_id' => $student->id,
            'supervisor_user_id' => Auth::id(),
            'coordinator_user_id' => $coordinatorId,
            'evaluation_month' => $validated['evaluation_month'],
            'evaluation_year' => $validated['evaluation_year'],
            'month_number' => $monthNumber,
            'student_name' => $student->name,
            'hte_name' => $company ? $company->name : 'N/A',
            'hte_address' => $company ? $company->address : 'N/A',
            'work_assignment' => $validated['work_assignment'],
            'work_schedule' => $acceptance ? ($acceptance->formatted_work_schedule ?? 'N/A') : 'N/A',
            'supervisor_name' => $acceptance && $acceptance->immediate_supervisor ? $acceptance->immediate_supervisor : Auth::user()->name,
            'rating_row_1' => $validated['rating_row_1'],
            'rating_row_2' => $validated['rating_row_2'],
            'rating_row_3' => $validated['rating_row_3'],
            'rating_row_4' => $validated['rating_row_4'],
            'rating_row_5' => $validated['rating_row_5'],
            'rating_row_6' => $validated['rating_row_6'],
            'rating_row_7' => $validated['rating_row_7'],
            'rating_row_8' => $validated['rating_row_8'],
            'rating_row_9' => $validated['rating_row_9'],
            'rating_row_10' => $validated['rating_row_10'],
            'rating_row_11' => $validated['rating_row_11'],
            'rating_row_12' => $validated['rating_row_12'],
            'rating_row_13' => $validated['rating_row_13'],
            'rating_row_14' => $validated['rating_row_14'],
            'rating_row_15' => $validated['rating_row_15'],
            'rating_row_16' => $validated['rating_row_16'],
            'rating_row_17' => $validated['rating_row_17'],
            'rating_row_18' => $validated['rating_row_18'],
            'rating_row_19' => $validated['rating_row_19'],
            'rating_row_20' => $validated['rating_row_20'],
            'comments_recommendations' => $validated['comments_recommendations'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
        AuditLog::log(
            'monthly_evaluation_submitted',
            'Supervisor submitted monthly evaluation',
            'MonthlyEvaluation',
            $evaluation->id,
            null,
            [
                'student_user_id' => (int) $student->id,
                'month' => (int) $evaluation->evaluation_month,
                'year' => (int) $evaluation->evaluation_year,
            ]
        );

        // Send notification to student
        $student->notify(new \App\Notifications\MonthlyEvaluationSubmitted($evaluation));

        // Send notification to coordinator if assigned
        if ($coordinatorId) {
            $coordinator = User::find($coordinatorId);
            if ($coordinator) {
                $coordinator->notify(new \App\Notifications\MonthlyEvaluationNeedsReview($evaluation));
            }
        }

        return redirect()->route('supervisor.students.view', $student)
            ->with('success', 'Monthly evaluation submitted successfully!');
    }

    public function show(MonthlyEvaluation $evaluation)
    {
        $this->authorize('viewAsSupervisor', $evaluation);

        $evaluation->load('student');

        return view('supervisor.evaluations.show', compact('evaluation'));
    }

    public function downloadPdf(MonthlyEvaluation $evaluation, \App\Services\MonthlyEvaluationPdfService $pdfService)
    {
        $this->authorize('viewAsSupervisor', $evaluation);

        $pdf = $pdfService->generate($evaluation);
        $fileName = sprintf(
            'monthly-evaluation-%s-%s-%s.pdf',
            $evaluation->student->studentProfile->student_id ?? 'student',
            $evaluation->getMonthName(),
            $evaluation->evaluation_year
        );

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
