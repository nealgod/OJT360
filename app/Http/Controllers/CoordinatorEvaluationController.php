<?php

namespace App\Http\Controllers;

use App\Models\MonthlyEvaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinatorEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $coordinator = Auth::user();
        $coordinatorProfile = $coordinator->coordinatorProfile;

        if (! $coordinatorProfile || ! $coordinatorProfile->program_id) {
            return view('coord.evaluations.index', [
                'evaluations' => collect(),
            ])->with('error', 'No program assigned to your coordinator profile.');
        }

        // Get students in coordinator's program
        $studentsQuery = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($query) use ($coordinatorProfile) {
                $query->where('program_id', $coordinatorProfile->program_id);
            });

        $studentIds = $studentsQuery->pluck('id');

        // Get evaluations for these students - ONLY SUBMITTED OR REVIEWED
        $evaluationsQuery = MonthlyEvaluation::with(['student.studentProfile'])
            ->whereIn('student_user_id', $studentIds)
            ->where('coordinator_user_id', $coordinator->id)
            ->whereIn('status', ['submitted', 'reviewed']);

        // Search by student ID only
        if ($request->filled('search')) {
            $search = $request->search;
            $evaluationsQuery->whereHas('student.studentProfile', function ($query) use ($search) {
                $query->where('student_id', 'like', "%{$search}%");
            });
        }

        $evaluationsQuery->orderByDesc('evaluation_year')
            ->orderByDesc('evaluation_month');

        $evaluations = $evaluationsQuery->paginate(15)->withQueryString();

        return view('coord.evaluations.index', compact('evaluations'));
    }

    public function show(MonthlyEvaluation $evaluation)
    {
        $this->authorize('viewAsCoordinator', $evaluation);

        $evaluation->load(['student.studentProfile', 'supervisor']);

        return view('coord.evaluations.show', compact('evaluation'));
    }

    public function downloadPdf(MonthlyEvaluation $evaluation, \App\Services\MonthlyEvaluationPdfService $pdfService)
    {
        $this->authorize('viewAsCoordinator', $evaluation);

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
        ]);
    }

    public function markReviewed(MonthlyEvaluation $evaluation)
    {
        $this->authorize('review', $evaluation);

        $evaluation->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Evaluation marked as reviewed.');
    }
}
