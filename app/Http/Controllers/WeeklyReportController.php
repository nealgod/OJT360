<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceLetter;
use App\Models\AttendanceLog;
use App\Models\WeeklyReport;
use App\Services\WeeklyReportPdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class WeeklyReportController extends Controller
{
    public function index()
    {
        $reports = WeeklyReport::forStudent(Auth::id())
            ->orderByDesc('week_start_date')
            ->paginate(10);

        return view('reports.weekly.index', compact('reports'));
    }

    public function create(Request $request)
    {
        // Get student's acceptance letter to determine internship period
        $acceptance = AcceptanceLetter::where('student_user_id', Auth::id())
            ->orderByDesc('start_date')
            ->first();

        if (!$acceptance || !$acceptance->start_date) {
            return redirect()->route('student.placement.show')
                ->with('error', 'You need an acceptance letter with a start date before creating weekly reports.');
        }

        // Get custom date range from request
        $startDateInput = $request->input('week_start_date') ?? session()->getOldInput('week_start_date');
        $endDateInput = $request->input('week_end_date') ?? session()->getOldInput('week_end_date');

        // Use custom dates or default to today
        $weekStart = $startDateInput ? Carbon::parse($startDateInput) : now();
        $weekEnd = $endDateInput ? Carbon::parse($endDateInput) : now();

        // Validate date range is within 7 days
        $daysDiff = $weekStart->diffInDays($weekEnd);
        if ($daysDiff > 6) {
            return redirect()->route('reports.weekly.index')
                ->with('error', 'Date range cannot exceed 7 days.');
        }

        // Ensure start is before or equal to end
        if ($weekStart->gt($weekEnd)) {
            return redirect()->route('reports.weekly.index')
                ->with('error', 'Start date must be before or equal to end date.');
        }

        // Check if dates are within internship period
        if ($weekStart->lt($acceptance->start_date)) {
            return redirect()->route('reports.weekly.index')
                ->with('error', 'Cannot create report for dates before your internship start date.');
        }

        if ($acceptance->end_date && $weekEnd->gt($acceptance->end_date)) {
            return redirect()->route('reports.weekly.index')
                ->with('error', 'Cannot create report for dates after your internship end date.');
        }

        // Check for overlapping reports with actual attendance
        $existingReports = WeeklyReport::forStudent(Auth::id())
            ->where(function ($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('week_start_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->orWhereBetween('week_end_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->orWhere(function ($q) use ($weekStart, $weekEnd) {
                        $q->where('week_start_date', '<=', $weekStart->toDateString())
                            ->where('week_end_date', '>=', $weekEnd->toDateString());
                    });
            })
            ->get();

        // Only block if there's an existing report with attendance on overlapping dates
        foreach ($existingReports as $report) {
            $reportStart = Carbon::parse($report->week_start_date);
            $reportEnd = Carbon::parse($report->week_end_date);
            
            // Find overlapping date range
            $overlapStart = $weekStart->greaterThan($reportStart) ? $weekStart : $reportStart;
            $overlapEnd = $weekEnd->lessThan($reportEnd) ? $weekEnd : $reportEnd;
            
            // Check if student had attendance during overlap
            $hasAttendance = AttendanceLog::where('student_user_id', Auth::id())
                ->whereBetween('work_date', [$overlapStart->toDateString(), $overlapEnd->toDateString()])
                ->whereNotNull('time_in')
                ->exists();
            
            if ($hasAttendance) {
                return redirect()->route('reports.weekly.show', $report)
                    ->with('info', 'You already have a report covering dates with attendance. You can only create new reports for dates you were absent.');
            }
        }

        // Check for incomplete attendance (time_in without time_out)
        $incompleteAttendance = AttendanceLog::where('student_user_id', Auth::id())
            ->whereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->get();

        if ($incompleteAttendance->isNotEmpty()) {
            $incompleteDates = $incompleteAttendance->map(function ($log) {
                return Carbon::parse($log->work_date)->format('M d, Y');
            })->join(', ');

            return redirect()->route('reports.weekly.index')
                ->with('error', 'Cannot create report yet. You have incomplete attendance (no time out) on: ' . $incompleteDates . '. Please complete your time out first or wait until the day is complete.');
        }

        $attendanceSummary = $this->getAttendanceSummary($weekStart, $weekEnd);
        $entries = $this->buildWeekEntriesFromAttendance($weekStart, $weekEnd);

        $oldEntries = session()->getOldInput('entries');
        if ($oldEntries) {
            $entries = $this->mergeOldEntries($entries, $oldEntries);
        }

        return view('reports.weekly.create', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'attendanceSummary' => $attendanceSummary,
            'entries' => $entries,
            'acceptance' => $acceptance,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'week_start_date' => ['required', 'date'],
            'week_end_date' => ['required', 'date', 'after_or_equal:week_start_date'],
            'problems_encountered' => ['nullable', 'string'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.date' => ['required', 'date'],
            'entries.*.activity' => ['nullable', 'string', 'max:1000'],
            'entries.*.hours' => ['nullable', 'numeric', 'min:0', 'max:24'],
        ]);

        // Check if at least one day with attendance has an activity
        $hasContent = collect($validated['entries'])->contains(function ($entry) {
            // Only check entries that have hours (meaning they have attendance)
            return !empty($entry['activity']) && (!empty($entry['hours']) && (float) $entry['hours'] > 0);
        });

        if (!$hasContent) {
            return back()
                ->withErrors(['entries' => 'Please add activities for at least one day with attendance.'])
                ->withInput();
        }

        $weekStart = Carbon::parse($validated['week_start_date']);
        $weekEnd = Carbon::parse($validated['week_end_date']);

        // Validate date range is within 7 days
        $daysDiff = $weekStart->diffInDays($weekEnd);
        if ($daysDiff > 6) {
            return back()
                ->withErrors(['week_end_date' => 'Date range cannot exceed 7 days.'])
                ->withInput();
        }

        // Check for overlapping reports with actual attendance
        $existingReports = WeeklyReport::forStudent(Auth::id())
            ->where(function ($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('week_start_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->orWhereBetween('week_end_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->orWhere(function ($q) use ($weekStart, $weekEnd) {
                        $q->where('week_start_date', '<=', $weekStart->toDateString())
                            ->where('week_end_date', '>=', $weekEnd->toDateString());
                    });
            })
            ->get();

        // Only block if there's an existing report with attendance on overlapping dates
        foreach ($existingReports as $report) {
            $reportStart = Carbon::parse($report->week_start_date);
            $reportEnd = Carbon::parse($report->week_end_date);
            
            // Find overlapping date range
            $overlapStart = $weekStart->greaterThan($reportStart) ? $weekStart : $reportStart;
            $overlapEnd = $weekEnd->lessThan($reportEnd) ? $weekEnd : $reportEnd;
            
            // Check if student had attendance during overlap
            $hasAttendance = AttendanceLog::where('student_user_id', Auth::id())
                ->whereBetween('work_date', [$overlapStart->toDateString(), $overlapEnd->toDateString()])
                ->whereNotNull('time_in')
                ->exists();
            
            if ($hasAttendance) {
                return redirect()->route('reports.weekly.index')
                    ->with('info', 'A report already exists covering dates with attendance. You can only create new reports for dates you were absent.');
            }
        }

        // Check for incomplete attendance (time_in without time_out)
        $incompleteAttendance = AttendanceLog::where('student_user_id', Auth::id())
            ->whereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->get();

        if ($incompleteAttendance->isNotEmpty()) {
            $incompleteDates = $incompleteAttendance->map(function ($log) {
                return Carbon::parse($log->work_date)->format('M d, Y');
            })->join(', ');

            return back()
                ->withErrors(['week_end_date' => 'Cannot submit report yet. You have incomplete attendance (no time out) on: ' . $incompleteDates . '. Please complete your time out first.'])
                ->withInput();
        }

        $attendanceSummary = $this->getAttendanceSummary($weekStart, $weekEnd);

        // Get coordinator for this student's program
        $coordinatorId = null;
        $studentProfile = Auth::user()->studentProfile;
        if ($studentProfile && $studentProfile->program_id) {
            $coordinator = \App\Models\User::where('role', 'coordinator')
                ->whereHas('coordinatorProfile', function($q) use ($studentProfile) {
                    $q->where('program_id', $studentProfile->program_id);
                })
                ->first();
            $coordinatorId = $coordinator?->id;
        }

        $report = WeeklyReport::create([
            'student_user_id' => Auth::id(),
            'coordinator_user_id' => $coordinatorId,
            'week_start_date' => $weekStart,
            'week_end_date' => $weekEnd,
            'week_number' => $this->calculateWeekNumber($weekStart),
            'days_present' => $attendanceSummary['days_present'],
            'days_absent' => $attendanceSummary['days_absent'],
            'days_late' => $attendanceSummary['days_late'],
            'total_hours' => $attendanceSummary['total_hours'],
            'entries' => $this->sanitizeEntries($validated['entries']),
            'problems_encountered' => $validated['problems_encountered'] ?? null,
            'status' => 'draft',
        ]);

        return redirect()->route('reports.weekly.show', $report)
            ->with('success', 'Weekly report saved as draft. You can review and submit it when ready.');
    }

    public function show(WeeklyReport $weekly)
    {
        if ($weekly->student_user_id !== Auth::id()) {
            abort(403);
        }

        return view('reports.weekly.show', [
            'report' => $weekly,
        ]);
    }

    public function downloadPdf(WeeklyReport $weekly, WeeklyReportPdfService $pdfService)
    {
        if ($weekly->student_user_id !== Auth::id()) {
            abort(403);
        }

        $pdf = $pdfService->generate($weekly);
        $fileName = sprintf('weekly-report-week-%s.pdf', $weekly->week_number);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$fileName}\"",
        ]);
    }

    private function getAttendanceSummary(Carbon $weekStart, Carbon $weekEnd): array
    {
        $logs = AttendanceLog::where('student_user_id', Auth::id())
            ->whereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();

        $hoursByDate = [];
        foreach ($logs as $log) {
            $dateKey = $log->work_date->toDateString();
            $hoursByDate[$dateKey] = round(($log->minutes_worked ?? 0) / 60, 2);
        }

        // Count days with attendance (any log with time_in or hours worked)
        $daysPresent = $logs->filter(function ($log) {
            return $log->time_in !== null || ($log->minutes_worked ?? 0) > 0;
        })->count();
        
        $daysLate = $logs->where('status', 'late')->count();
        
        // Calculate total days in selected range
        $totalDaysInRange = $weekStart->diffInDays($weekEnd) + 1;
        
        // Days absent = total days in range - days present
        $daysAbsent = max(0, $totalDaysInRange - $daysPresent);

        return [
            'days_present' => $daysPresent,
            'days_absent' => $daysAbsent,
            'days_late' => $daysLate,
            'total_hours' => round($logs->sum('minutes_worked') / 60, 2),
            'hours_by_date' => $hoursByDate,
        ];
    }



    private function buildWeekEntriesFromAttendance(Carbon $weekStart, Carbon $weekEnd): array
    {
        // Get actual attendance logs for the week
        $logs = AttendanceLog::where('student_user_id', Auth::id())
            ->whereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('work_date')
            ->get()
            ->keyBy(function ($log) {
                return $log->work_date->toDateString();
            });

        $entries = [];
        $current = $weekStart->copy();

        // Loop through all days in the selected range
        while ($current->lte($weekEnd)) {
            $dateKey = $current->toDateString();
            $log = $logs->get($dateKey);

            $entries[] = [
                'date' => $dateKey,
                'label' => $current->format('l, M d, Y'),
                'hours' => $log ? round(($log->minutes_worked ?? 0) / 60, 2) : 0,
                'activity' => '',
                'has_attendance' => $log !== null,
            ];

            $current->addDay();
        }

        return $entries;
    }

    private function sanitizeEntries(array $entries): array
    {
        return collect($entries)
            ->take(8)
            ->map(function ($entry) {
                return [
                    'date' => $entry['date'],
                    'activity' => $entry['activity'] ?? '',
                    'hours' => isset($entry['hours']) ? round((float) $entry['hours'], 2) : '',
                ];
            })
            ->values()
            ->all();
    }

    private function calculateWeekNumber(Carbon $weekStart): int
    {
        $acceptance = AcceptanceLetter::where('student_user_id', Auth::id())
            ->orderByDesc('start_date')
            ->first();

        $reference = $acceptance?->start_date
            ? $acceptance->start_date->copy()->startOfWeek(Carbon::MONDAY)
            : $weekStart->copy()->startOfWeek(Carbon::MONDAY);

        return $weekStart->copy()->startOfWeek(Carbon::MONDAY)->diffInWeeks($reference) + 1;
    }

    private function mergeOldEntries(array $defaultEntries, array $oldEntries): array
    {
        return collect($defaultEntries)
            ->map(function ($entry, $index) use ($oldEntries) {
                if (isset($oldEntries[$index])) {
                    $entry['activity'] = $oldEntries[$index]['activity'] ?? $entry['activity'];
                    $entry['hours'] = $oldEntries[$index]['hours'] ?? $entry['hours'];
                }
                return $entry;
            })
            ->all();
    }

    public function submit(WeeklyReport $weekly)
    {
        // Verify ownership
        if ($weekly->student_user_id !== Auth::id()) {
            abort(403);
        }

        // Check if already submitted
        if ($weekly->status !== 'draft') {
            return redirect()->route('reports.weekly.show', $weekly)
                ->with('error', 'This report has already been submitted.');
        }

        // Validate report has content
        if (empty($weekly->entries) || count($weekly->entries) === 0) {
            return redirect()->route('reports.weekly.show', $weekly)
                ->with('error', 'Cannot submit an empty report. Please add activities first.');
        }

        // Check if at least one entry has activity
        $hasActivity = collect($weekly->entries)->some(function ($entry) {
            return !empty($entry['activity']);
        });

        if (!$hasActivity) {
            return redirect()->route('reports.weekly.show', $weekly)
                ->with('error', 'Please add at least one activity before submitting.');
        }

        // Update status to submitted
        $old = $weekly->getOriginal();
        $weekly->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);
        AuditLog::log(
            'weekly_submitted',
            'Student submitted weekly report',
            'WeeklyReport',
            $weekly->id,
            $old,
            [
                'week_number' => (int) $weekly->week_number,
                'status' => (string) $weekly->status,
            ]
        );

        return redirect()->route('reports.weekly.show', $weekly)
            ->with('success', 'Weekly report submitted successfully! Your coordinator will review it.');
    }

    public function destroy(WeeklyReport $weekly)
    {
        // Verify ownership
        if ($weekly->student_user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow deletion of draft reports
        if ($weekly->status !== 'draft') {
            return redirect()->route('reports.weekly.index')
                ->with('error', 'Cannot delete a report that has already been submitted. Please contact your coordinator.');
        }

        $weekNumber = $weekly->week_number;
        $weekly->delete();

        return redirect()->route('reports.weekly.index')
            ->with('success', "Weekly report (Week {$weekNumber}) has been deleted successfully.");
    }
}
