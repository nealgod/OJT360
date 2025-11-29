<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceLetter;
use App\Models\FinalEvaluation;
use App\Models\User;
use App\Models\WeeklyReport;
use App\Services\FinalEvaluationPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorFinalEvaluationController extends Controller
{
    public function index()
    {
        $evaluations = FinalEvaluation::forSupervisor(Auth::id())
            ->with('student.studentProfile')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('supervisor.final-evaluations.index', compact('evaluations'));
    }

    public function create(User $student)
    {
        $this->authorize('create', [FinalEvaluation::class, $student]);

        // Get student profile
        $profile = $student->studentProfile;
        if (! $profile) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Get acceptance letter
        $acceptance = AcceptanceLetter::where('student_user_id', $student->id)
            ->with('company')
            ->latest('start_date')
            ->first();

        if (! $acceptance) {
            return redirect()->back()->with('error', 'Student must have an acceptance letter.');
        }

        // Calculate total hours from weekly reports
        $totalHours = WeeklyReport::where('student_user_id', $student->id)
            ->whereIn('status', ['submitted', 'reviewed'])
            ->sum('total_hours');

        // Get company info
        $company = $acceptance->company;

        return view('supervisor.final-evaluations.create', compact(
            'student',
            'profile',
            'acceptance',
            'company',
            'totalHours'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'rating_quality_thoroughness' => 'required|numeric|min:0|max:20',
            'rating_dependability' => 'required|numeric|min:0|max:15',
            'rating_quality_completion' => 'required|numeric|min:0|max:20',
            'rating_attendance' => 'required|numeric|min:0|max:15',
            'rating_cooperation' => 'required|numeric|min:0|max:10',
            'rating_judgement' => 'required|numeric|min:0|max:10',
            'rating_personality' => 'required|numeric|min:0|max:5',
            'comments_recommendations' => 'nullable|string|max:300',
        ], [
            'rating_quality_thoroughness.max' => 'Quality of work (Thoroughness) rating cannot exceed 20%.',
            'rating_dependability.max' => 'Dependability rating cannot exceed 15%.',
            'rating_quality_completion.max' => 'Quality of work (Completion) rating cannot exceed 20%.',
            'rating_attendance.max' => 'Attendance rating cannot exceed 15%.',
            'rating_cooperation.max' => 'Cooperation rating cannot exceed 10%.',
            'rating_judgement.max' => 'Judgement rating cannot exceed 10%.',
            'rating_personality.max' => 'Personality rating cannot exceed 5%.',
            'rating_quality_thoroughness.required' => 'Quality of work (Thoroughness) rating is required.',
            'rating_dependability.required' => 'Dependability rating is required.',
            'rating_quality_completion.required' => 'Quality of work (Completion) rating is required.',
            'rating_attendance.required' => 'Attendance rating is required.',
            'rating_cooperation.required' => 'Cooperation rating is required.',
            'rating_judgement.required' => 'Judgement rating is required.',
            'rating_personality.required' => 'Personality rating is required.',
            'comments_recommendations.max' => 'Comments and recommendations cannot exceed 300 characters.',
        ]);

        // Check for duplicate
        $existing = FinalEvaluation::where('student_user_id', $validated['student_user_id'])->first();
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Final evaluation already exists for this student.']);
        }

        // Get student info
        $student = User::findOrFail($validated['student_user_id']);
        $profile = $student->studentProfile;
        $acceptance = AcceptanceLetter::where('student_user_id', $student->id)
            ->with('company')
            ->latest('start_date')
            ->first();

        // Calculate total hours
        $totalHours = WeeklyReport::where('student_user_id', $student->id)
            ->whereIn('status', ['submitted', 'reviewed'])
            ->sum('total_hours');

        // Calculate total rating
        $totalRating = $validated['rating_quality_thoroughness'] +
                      $validated['rating_dependability'] +
                      $validated['rating_quality_completion'] +
                      $validated['rating_attendance'] +
                      $validated['rating_cooperation'] +
                      $validated['rating_judgement'] +
                      $validated['rating_personality'];

        // Get coordinator
        $coordinatorId = null;
        if ($profile && $profile->program_id) {
            $coordinator = User::where('role', 'coordinator')
                ->whereHas('coordinatorProfile', function ($q) use ($profile) {
                    $q->where('program_id', $profile->program_id);
                })
                ->first();
            $coordinatorId = $coordinator?->id;
        }

        // Create evaluation
        $evaluation = FinalEvaluation::create([
            'student_user_id' => $student->id,
            'supervisor_user_id' => Auth::id(),
            'coordinator_user_id' => $coordinatorId,
            'control_number' => FinalEvaluation::generateControlNumber($profile->student_id ?? 'UNKNOWN'),
            'revision_number' => 1,
            'student_name' => $student->name,
            'student_id' => $profile->student_id ?? 'N/A',
            'course' => $profile->course ?? 'N/A',
            'department' => $profile->department ?? 'N/A',
            'hte_name' => $acceptance && $acceptance->company ? $acceptance->company->name : 'N/A',
            'hte_address' => $acceptance && $acceptance->company ? $acceptance->company->address : 'N/A',
            'internship_start_date' => $acceptance?->start_date,
            'internship_end_date' => $acceptance?->end_date,
            'total_hours_rendered' => $totalHours,
            'rating_quality_thoroughness' => $validated['rating_quality_thoroughness'],
            'rating_dependability' => $validated['rating_dependability'],
            'rating_quality_completion' => $validated['rating_quality_completion'],
            'rating_attendance' => $validated['rating_attendance'],
            'rating_cooperation' => $validated['rating_cooperation'],
            'rating_judgement' => $validated['rating_judgement'],
            'rating_personality' => $validated['rating_personality'],
            'total_rating' => $totalRating,
            'comments_recommendations' => $validated['comments_recommendations'],
            'supervisor_name' => Auth::user()->name,
            'supervisor_signature_date' => now(),
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Send notifications using custom Notification model
        \App\Models\Notification::create([
            'user_id' => $student->id,
            'type' => 'final_evaluation_submitted',
            'title' => 'Final Evaluation Completed',
            'message' => 'Your supervisor has submitted your final OJT performance evaluation.',
            'data' => [
                'evaluation_id' => $evaluation->id,
                'url' => route('evaluations.final.status'),
            ],
            'read' => false,
        ]);

        if ($coordinatorId) {
            \App\Models\Notification::create([
                'user_id' => $coordinatorId,
                'type' => 'final_evaluation_review',
                'title' => 'Final Evaluation Needs Review',
                'message' => 'A final evaluation for '.$student->name.' has been submitted and needs your review.',
                'data' => [
                    'evaluation_id' => $evaluation->id,
                    'url' => route('coordinator.final-evaluations.show', $evaluation),
                ],
                'read' => false,
            ]);
        }

        return redirect()->route('supervisor.students.view', $student)
            ->with('success', 'Final evaluation submitted successfully!');
    }

    public function show(FinalEvaluation $evaluation)
    {
        $this->authorize('viewAsSupervisor', $evaluation);

        $evaluation->load('student.studentProfile', 'coordinator');

        return view('supervisor.final-evaluations.show', compact('evaluation'));
    }

    public function downloadPdf(Request $request, FinalEvaluation $evaluation, FinalEvaluationPdfService $pdfService)
    {
        $this->authorize('viewAsSupervisor', $evaluation);

        $pdf = $pdfService->generate($evaluation);
        $fileName = sprintf(
            'final-evaluation-%s-%s.pdf',
            $evaluation->student_id,
            $evaluation->control_number
        );

        $disposition = $request->has('view') ? 'inline' : 'attachment';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "{$disposition}; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
