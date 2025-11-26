<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Company;
use App\Models\FinalEvaluation;
use App\Models\MonthlyEvaluation;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index()
    {
        // Overall statistics
        $stats = [
            'total_users' => User::count(),
            'total_interns' => User::where('role', 'intern')->count(),
            'active_interns' => User::where('role', 'intern')
                ->whereHas('studentProfile', fn ($q) => $q->where('ojt_status', 'active'))
                ->count(),
            'total_coordinators' => User::where('role', 'coordinator')->count(),
            'total_supervisors' => User::where('role', 'supervisor')->count(),
            'total_companies' => Company::count(),
            'total_attendance_logs' => AttendanceLog::count(),
            'total_hours' => round(AttendanceLog::sum('minutes_worked') / 60, 1),
            'total_weekly_reports' => WeeklyReport::count(),
            'total_monthly_evaluations' => MonthlyEvaluation::count(),
            'total_final_evaluations' => FinalEvaluation::count(),
        ];

        // Recent activity
        $recentAttendance = AttendanceLog::with('user')
            ->latest('work_date')
            ->limit(10)
            ->get();

        $recentReports = WeeklyReport::with('student')
            ->latest('week_start_date')
            ->limit(10)
            ->get();

        // Top interns by hours
        $topInterns = User::where('role', 'intern')
            ->withSum('attendanceLogs as total_minutes', 'minutes_worked')
            ->orderByDesc('total_minutes')
            ->limit(10)
            ->get();

        // Monthly trends
        $monthlyData = AttendanceLog::selectRaw('
                DATE_FORMAT(work_date, "%Y-%m") as month,
                COUNT(DISTINCT student_user_id) as active_interns,
                SUM(minutes_worked) / 60 as total_hours
            ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return view('admin.reports.index', compact(
            'stats',
            'recentAttendance',
            'recentReports',
            'topInterns',
            'monthlyData'
        ));
    }

    public function attendance(Request $request)
    {
        $query = AttendanceLog::with(['user.studentProfile']);

        if ($request->filled('date_from')) {
            $query->where('work_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('work_date', '<=', $request->date_to);
        }

        if ($request->filled('user_id')) {
            $query->where('student_user_id', $request->user_id);
        }

        $logs = $query->orderBy('work_date', 'desc')->paginate(50);
        $interns = User::where('role', 'intern')->orderBy('name')->get();

        return view('admin.reports.attendance', compact('logs', 'interns'));
    }

    public function weeklyReports(Request $request)
    {
        $query = WeeklyReport::with(['student', 'coordinator']);

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereNull('coordinator_reviewed_at');
            } elseif ($request->status === 'reviewed') {
                $query->whereNotNull('coordinator_reviewed_at');
            }
        }

        $reports = $query->orderBy('week_start_date', 'desc')->paginate(30);

        return view('admin.reports.weekly', compact('reports'));
    }

    public function evaluations(Request $request)
    {
        $monthlyEvals = MonthlyEvaluation::with(['student', 'supervisor', 'coordinator'])
            ->orderBy('evaluation_month', 'desc')
            ->limit(20)
            ->get();

        $finalEvals = FinalEvaluation::with(['student', 'supervisor', 'coordinator'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.reports.evaluations', compact('monthlyEvals', 'finalEvals'));
    }
}
