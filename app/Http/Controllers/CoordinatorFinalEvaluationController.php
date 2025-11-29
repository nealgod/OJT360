<?php

namespace App\Http\Controllers;

use App\Models\FinalEvaluation;
use App\Models\User;
use App\Services\FinalEvaluationPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinatorFinalEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $coordinator = Auth::user();
        $coordinatorProfile = $coordinator->coordinatorProfile;

        if (! $coordinatorProfile || ! $coordinatorProfile->program_id) {
            return view('coord.final-evaluations.index', [
                'evaluations' => collect(),
            ])->with('error', 'No program assigned to your coordinator profile.');
        }

        // Get students in coordinator's program
        $studentIds = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($query) use ($coordinatorProfile) {
                $query->where('program_id', $coordinatorProfile->program_id);
            })
            ->pluck('id');

        // Get final evaluations for these students
        $evaluationsQuery = FinalEvaluation::with(['student.studentProfile', 'supervisor'])
            ->whereIn('student_user_id', $studentIds)
            ->whereIn('status', ['submitted', 'reviewed']);

        // Search by student ID or name
        if ($request->filled('search')) {
            $search = $request->search;
            $evaluationsQuery->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $evaluationsQuery->orderByDesc('submitted_at');

        $evaluations = $evaluationsQuery->paginate(15)->withQueryString();

        return view('coord.final-evaluations.index', compact('evaluations'));
    }

    public function show(FinalEvaluation $evaluation)
    {
        $this->authorize('viewAsCoordinator', $evaluation);

        $evaluation->load(['student.studentProfile', 'supervisor']);

        return view('coord.final-evaluations.show', compact('evaluation'));
    }

    public function markReviewed(FinalEvaluation $evaluation)
    {
        $this->authorize('review', $evaluation);

        $evaluation->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Final evaluation marked as reviewed.');
    }

    public function downloadPdf(Request $request, FinalEvaluation $evaluation, FinalEvaluationPdfService $pdfService)
    {
        $this->authorize('viewAsCoordinator', $evaluation);

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
        ]);
    }
}
