<?php

namespace App\Http\Controllers;

use App\Models\WeeklyReport;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CoordinatorReportController extends Controller
{
    public function index(Request $request)
    {
        $coordinator = Auth::user();
        $coordinatorProfile = $coordinator->coordinatorProfile;
        
        if (!$coordinatorProfile || !$coordinatorProfile->program_id) {
            return view('coord.reports.index', [
                'reports' => collect(),
            ])->with('error', 'No program assigned to your coordinator profile.');
        }
        
        // Get students in coordinator's program
        $studentsQuery = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($query) use ($coordinatorProfile) {
                $query->where('program_id', $coordinatorProfile->program_id);
            });
        
        $studentIds = $studentsQuery->pluck('id');
        
        // Get weekly reports for these students - ONLY SUBMITTED OR REVIEWED
        $reportsQuery = WeeklyReport::with(['student.studentProfile'])
            ->whereIn('student_user_id', $studentIds)
            ->where('coordinator_user_id', $coordinator->id)
            ->whereIn('status', ['submitted', 'reviewed']);
        
        // Search by student ID only
        if ($request->filled('search')) {
            $search = $request->search;
            $reportsQuery->whereHas('student.studentProfile', function ($query) use ($search) {
                $query->where('student_id', 'like', "%{$search}%");
            });
        }
        
        $reportsQuery->orderBy('created_at', 'desc');
        
        $reports = $reportsQuery->paginate(15)->withQueryString();
        
        return view('coord.reports.index', compact('reports'));
    }
    
    public function show(WeeklyReport $report)
    {
        $this->authorize('viewAsCoordinator', $report);
        
        $report->load(['student.studentProfile.supervisor']);
        
        return view('coord.reports.show', compact('report'));
    }
    
    public function updateStatus(Request $request, WeeklyReport $report)
    {
        $this->authorize('updateStatus', $report);
        
        $request->validate([
            'status' => 'required|in:draft,submitted,reviewed',
            'coordinator_feedback' => 'nullable|string|max:1000'
        ]);
        
        $old = $report->getOriginal();
        $report->update([
            'status' => $request->status,
            'coordinator_feedback' => $request->coordinator_feedback,
            'coordinator_reviewed_at' => now()
        ]);
        AuditLog::log(
            'weekly_review_updated',
            'Coordinator updated weekly report status',
            'WeeklyReport',
            $report->id,
            $old,
            [
                'status' => (string) $report->status,
            ]
        );
        
        return redirect()->back()->with('success', 'Report status updated successfully.');
    }

    public function downloadPdf(WeeklyReport $report, \App\Services\WeeklyReportPdfService $pdfService)
    {
        $this->authorize('viewAsCoordinator', $report);
        
        $pdf = $pdfService->generate($report);
        $fileName = sprintf('weekly-report-week-%s-%s.pdf', $report->week_number, $report->student->studentProfile->student_id ?? 'student');

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$fileName}\"",
        ]);
    }
}
