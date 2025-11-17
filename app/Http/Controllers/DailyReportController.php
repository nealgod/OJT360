<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyReportController extends Controller
{
    public function index()
    {
        $reports = DailyReport::where('student_user_id', Auth::id())
            ->orderByDesc('work_date')
            ->paginate(10);
        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_date' => ['required', 'date', 'before_or_equal:today'],
            'summary' => ['required', 'string', 'min:50', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:6144'],
        ]);

        // Check for duplicate report on the same date
        $existingReport = DailyReport::where('student_user_id', Auth::id())
            ->where('work_date', $request->date('work_date'))
            ->first();

        if ($existingReport) {
            return back()->withErrors(['work_date' => 'You have already submitted a report for this date.']);
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('daily-reports', 'public');
        }

        $report = DailyReport::create([
            'student_user_id' => Auth::id(),
            'work_date' => $request->date('work_date'),
            'summary' => $request->string('summary'),
            'attachment_path' => $path,
        ]);

        // Notify coordinator responsible for this student's program/department
        $student = Auth::user();
        $coordinator = \App\Models\User::where('role', 'coordinator')
            ->whereHas('coordinatorProfile', function($q) use ($student) {
                $q->where('department', $student->studentProfile?->department);
            })
            ->first();
        if ($coordinator) {
            \App\Models\Notification::create([
                'user_id' => $coordinator->id,
                'type' => 'daily_report_submitted',
                'title' => 'New Daily Report',
                'message' => $student->name . ' submitted a daily report for ' . $request->date('work_date')->format('M d, Y') . '.',
                'data' => [ 'report_id' => $report->id, 'student_user_id' => $student->id ],
            ]);
        }

        return redirect()->route('reports.index')->with('success', 'Daily report submitted successfully!');
    }

    public function show(DailyReport $report)
    {
        $user = Auth::user();
        $isOwner = $report->student_user_id === $user->id;
        $isCoordinator = $user->isCoordinator() && $report->student->studentProfile?->department === $user->coordinatorProfile?->department && (
            empty(optional($user->coordinatorProfile?->program)->name) ||
            optional($user->coordinatorProfile?->program)->name === $report->student->studentProfile?->course
        );

        abort_unless($isOwner || $isCoordinator, 403);

        // Load attendance data for this report's work date
        $attendance = \App\Models\AttendanceLog::where('student_user_id', $user->id)
            ->whereDate('work_date', $report->work_date)
            ->first();

        return view('reports.show', compact('report', 'attendance'));
    }

    public function destroy(DailyReport $report)
    {
        $user = Auth::user();
        abort_unless($report->student_user_id === $user->id, 403);
        abort_unless($report->status === 'submitted', 403, 'Cannot delete approved or returned reports.');

        // Delete attachment if exists
        if ($report->attachment_path && Storage::disk('public')->exists($report->attachment_path)) {
            Storage::disk('public')->delete($report->attachment_path);
        }

        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Daily report deleted successfully!');
    }

    public function weekly()
    {
        $user = Auth::user();
        
        // Get the current week's reports
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $weeklyReports = DailyReport::where('student_user_id', $user->id)
            ->whereBetween('work_date', [$startOfWeek, $endOfWeek])
            ->orderBy('work_date')
            ->get();

        // Get all weeks with reports for the dropdown
        $weeksWithReports = DailyReport::where('student_user_id', $user->id)
            ->selectRaw('DATE(DATE_SUB(work_date, INTERVAL WEEKDAY(work_date) DAY)) as week_start')
            ->groupBy('week_start')
            ->orderByDesc('week_start')
            ->get();

        return view('reports.weekly', compact('weeklyReports', 'weeksWithReports', 'startOfWeek', 'endOfWeek'));
    }

    public function generateWeekly(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'week_start' => ['required', 'date'],
        ]);

        $weekStart = $request->date('week_start');
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        $reports = DailyReport::where('student_user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->orderBy('work_date')
            ->get();

        if ($reports->isEmpty()) {
            return back()->withErrors(['week_start' => 'No reports found for the selected week.']);
        }

        // Calculate weekly statistics
        $totalDays = $reports->count();
        $approvedReports = $reports->where('status', 'approved')->count();
        $returnedReports = $reports->where('status', 'returned')->count();

        // Calculate total hours from attendance for this week
        $attendanceLogs = \App\Models\AttendanceLog::where('student_user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get();
        
        $totalHours = $attendanceLogs->sum('minutes_worked') / 60; // Convert minutes to hours

        // Generate comprehensive weekly summary
        $weeklySummary = $this->generateWeeklySummary($reports, $user);

        // Show preview
        return view('reports.weekly-preview', compact('reports', 'weeklySummary', 'weekStart', 'weekEnd', 'totalDays', 'approvedReports', 'returnedReports', 'totalHours'));
    }

    public function downloadWeekly(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'week_start' => ['required', 'date'],
        ]);

        $weekStart = $request->date('week_start');
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        $reports = DailyReport::where('student_user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->orderBy('work_date')
            ->get();

        if ($reports->isEmpty()) {
            return back()->withErrors(['week_start' => 'No reports found for the selected week.']);
        }

        // Calculate weekly statistics
        $totalDays = $reports->count();
        $approvedReports = $reports->where('status', 'approved')->count();
        $returnedReports = $reports->where('status', 'returned')->count();

        // Calculate total hours from attendance for this week
        $attendanceLogs = \App\Models\AttendanceLog::where('student_user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get();
        
        $totalHours = $attendanceLogs->sum('minutes_worked') / 60; // Convert minutes to hours

        // Generate comprehensive weekly summary
        $weeklySummary = $this->generateWeeklySummary($reports, $user);

        // Generate PDF
        $pdf = Pdf::loadView('reports.weekly-pdf', compact('reports', 'weeklySummary', 'weekStart', 'weekEnd', 'totalDays', 'approvedReports', 'returnedReports', 'totalHours'));
        $filename = 'Weekly_Report_' . $weekStart->format('Y-m-d') . '_to_' . $weekEnd->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function submitWeeklyToDocuments(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'week_start' => ['required', 'date'],
        ]);

        $weekStart = $request->date('week_start');
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        $reports = DailyReport::where('student_user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->orderBy('work_date')
            ->get();

        if ($reports->isEmpty()) {
            return back()->withErrors(['week_start' => 'No reports found for the selected week.']);
        }

        // Calculate weekly statistics
        $totalDays = $reports->count();
        $approvedReports = $reports->where('status', 'approved')->count();
        $returnedReports = $reports->where('status', 'returned')->count();

        // Calculate total hours from attendance for this week
        $attendanceLogs = \App\Models\AttendanceLog::where('student_user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get();
        
        $totalHours = $attendanceLogs->sum('minutes_worked') / 60; // Convert minutes to hours

        // Generate comprehensive weekly summary
        $weeklySummary = $this->generateWeeklySummary($reports, $user);

        // Generate PDF
        $pdf = Pdf::loadView('reports.weekly-pdf', compact('reports', 'weeklySummary', 'weekStart', 'weekEnd', 'totalDays', 'approvedReports', 'returnedReports', 'totalHours'));
        
        // Save to storage
        $filename = 'Weekly_Report_' . $weekStart->format('Y-m-d') . '_to_' . $weekEnd->format('Y-m-d') . '.pdf';
        $filePath = 'document-submissions/' . $user->id . '/' . $filename;
        
        // Save PDF to storage
        Storage::disk('public')->put($filePath, $pdf->output());
        
        // Get file size
        $fileSize = Storage::disk('public')->size($filePath);

        // Find the "Weekly Accomplishment Report" document requirement
        $weeklyRequirement = \App\Models\DocumentRequirement::where('name', 'LIKE', '%Weekly Accomplishment Report%')
            ->where('type', 'post_placement')
            ->first();

        if (!$weeklyRequirement) {
            return back()->withErrors(['error' => 'Weekly Accomplishment Report requirement not found in system.']);
        }

        // Check if already submitted for this week
        $existingSubmission = \App\Models\StudentDocumentSubmission::where('student_user_id', $user->id)
            ->where('document_requirement_id', $weeklyRequirement->id)
            ->where('file_path', $filePath)
            ->first();

        if ($existingSubmission) {
            return back()->withErrors(['error' => 'Weekly report for this week has already been submitted.']);
        }

        // Create document submission
        $submission = \App\Models\StudentDocumentSubmission::create([
            'student_user_id' => $user->id,
            'document_requirement_id' => $weeklyRequirement->id,
            'file_path' => $filePath,
            'original_filename' => $filename,
            'file_size' => $fileSize,
            'mime_type' => 'application/pdf',
            'status' => 'submitted',
        ]);

        // Notify coordinator
        $coordinator = \App\Models\User::where('role', 'coordinator')
            ->whereHas('coordinatorProfile', function($q) use ($user) {
                $q->where('department', $user->studentProfile?->department);
            })
            ->first();

        if ($coordinator) {
            \App\Models\Notification::create([
                'user_id' => $coordinator->id,
                'type' => 'document_submitted',
                'title' => 'Weekly Report Submitted',
                'message' => $user->name . ' submitted a weekly accomplishment report for ' . $weekStart->format('M d, Y') . ' - ' . $weekEnd->format('M d, Y') . '.',
                'data' => [
                    'submission_id' => $submission->id,
                    'student_user_id' => $user->id,
                ],
            ]);
        }

        return redirect()->route('documents.index')->with('success', 'Weekly report submitted successfully! It has been added to your document submissions for coordinator review.');
    }

    private function generateWeeklySummary($reports, $user)
    {
        $summary = [];
        
        // Key accomplishments
        $accomplishments = [];
        foreach ($reports as $report) {
            if ($report->status === 'approved') {
                $accomplishments[] = $report->summary;
            }
        }
        
        // Skills learned
        $skills = [];
        foreach ($reports as $report) {
            if (preg_match_all('/learned|skill|training|taught|mastered/i', $report->summary, $matches)) {
                $skills[] = $report->summary;
            }
        }
        
        // Challenges faced
        $challenges = [];
        foreach ($reports as $report) {
            if (preg_match_all('/challenge|difficult|problem|issue|struggle/i', $report->summary, $matches)) {
                $challenges[] = $report->summary;
            }
        }

        return [
            'accomplishments' => $accomplishments,
            'skills_learned' => $skills,
            'challenges' => $challenges,
            'total_hours' => $reports->count() * 8,
            'attendance_rate' => round(($reports->count() / 5) * 100, 1),
        ];
    }
}


