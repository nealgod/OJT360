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
            'active_interns' => User::where('role', 'intern')
                ->whereHas('studentProfile', fn ($q) => $q->where('ojt_status', 'active'))
                ->count(),
            'total_interns' => User::where('role', 'intern')->count(),
            'total_hours' => round(AttendanceLog::sum('minutes_worked') / 60, 1),
            'pending_weekly_reports' => WeeklyReport::whereNull('coordinator_reviewed_at')->count(),
            'pending_monthly_evaluations' => MonthlyEvaluation::whereNull('reviewed_at')->count(),
            'pending_final_evaluations' => FinalEvaluation::whereNull('reviewed_at')->count(),
            'total_companies' => Company::count(),
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
        try {
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

            if ($request->filled('department_id')) {
                $query->whereHas('user.studentProfile', fn($q) => $q->where('department_id', $request->department_id));
            }

            if ($request->filled('program_id')) {
                $query->whereHas('user.studentProfile', fn($q) => $q->where('program_id', $request->program_id));
            }

            $logs = $query->orderBy('work_date', 'desc')->paginate(50);
            $interns = User::where('role', 'intern')->with('studentProfile')->orderBy('name')->get();
            $departments = \App\Models\Department::orderBy('name')->get();
            $programs = \App\Models\Program::orderBy('name')->get();

            return view('admin.reports.attendance', compact('logs', 'interns', 'departments', 'programs'));
        } catch (\Exception $e) {
            \Log::error('Admin attendance report error: ' . $e->getMessage());
            
            $interns = User::where('role', 'intern')->with('studentProfile')->orderBy('name')->get();
            $departments = \App\Models\Department::orderBy('name')->get();
            $programs = \App\Models\Program::orderBy('name')->get();
            $logs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50);
            
            return view('admin.reports.attendance', compact('logs', 'interns', 'departments', 'programs'))
                ->with('error', 'No attendance records found matching your filters. Please try different criteria.');
        }
    }

    public function weeklyReports(Request $request)
    {
        try {
            $query = WeeklyReport::with(['student', 'coordinator']);

            if ($request->filled('department_id')) {
                $query->whereHas('student.studentProfile', fn($q) => $q->where('department_id', $request->department_id));
            }

            if ($request->filled('program_id')) {
                $query->whereHas('student.studentProfile', fn($q) => $q->where('program_id', $request->program_id));
            }

            if ($request->filled('student_id')) {
                $query->where('student_user_id', $request->student_id);
            }

            $reports = $query->orderBy('week_start_date', 'desc')->paginate(30);
            $departments = \App\Models\Department::orderBy('name')->get();
            $programs = \App\Models\Program::orderBy('name')->get();
            $students = User::where('role', 'intern')->with('studentProfile')->orderBy('name')->get();

            return view('admin.reports.weekly', compact('reports', 'departments', 'programs', 'students'));
        } catch (\Exception $e) {
            \Log::error('Admin weekly reports error: ' . $e->getMessage());
            
            $departments = \App\Models\Department::orderBy('name')->get();
            $programs = \App\Models\Program::orderBy('name')->get();
            $students = User::where('role', 'intern')->with('studentProfile')->orderBy('name')->get();
            $reports = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 30);
            
            return view('admin.reports.weekly', compact('reports', 'departments', 'programs', 'students'))
                ->with('error', 'No weekly reports found matching your filters. Please try different criteria.');
        }
    }

    public function evaluations(Request $request)
    {
        try {
            $monthlyQuery = MonthlyEvaluation::with(['student', 'supervisor', 'coordinator']);
            $finalQuery = FinalEvaluation::with(['student', 'supervisor', 'coordinator']);

            if ($request->filled('department_id')) {
                $monthlyQuery->whereHas('student.studentProfile', fn($q) => $q->where('department_id', $request->department_id));
                $finalQuery->whereHas('student.studentProfile', fn($q) => $q->where('department_id', $request->department_id));
            }

            if ($request->filled('program_id')) {
                $monthlyQuery->whereHas('student.studentProfile', fn($q) => $q->where('program_id', $request->program_id));
                $finalQuery->whereHas('student.studentProfile', fn($q) => $q->where('program_id', $request->program_id));
            }

            if ($request->filled('student_id')) {
                $monthlyQuery->where('student_user_id', $request->student_id);
                $finalQuery->where('student_user_id', $request->student_id);
            }

            $monthlyEvals = $monthlyQuery->orderByDesc('evaluation_year')
                ->orderByDesc('evaluation_month')
                ->paginate(20, ['*'], 'monthly_page');

            $finalEvals = $finalQuery->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'final_page');

            $departments = \App\Models\Department::orderBy('name')->get();
            $programs = \App\Models\Program::orderBy('name')->get();
            $students = User::where('role', 'intern')->with('studentProfile')->orderBy('name')->get();

            return view('admin.reports.evaluations', compact('monthlyEvals', 'finalEvals', 'departments', 'programs', 'students'));
        } catch (\Exception $e) {
            \Log::error('Admin evaluations report error: ' . $e->getMessage());
            
            $departments = \App\Models\Department::orderBy('name')->get();
            $programs = \App\Models\Program::orderBy('name')->get();
            $students = User::where('role', 'intern')->with('studentProfile')->orderBy('name')->get();
            $monthlyEvals = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1, ['pageName' => 'monthly_page']);
            $finalEvals = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1, ['pageName' => 'final_page']);
            
            return view('admin.reports.evaluations', compact('monthlyEvals', 'finalEvals', 'departments', 'programs', 'students'))
                ->with('error', 'No evaluations found matching your filters. Please try different criteria.');
        }
    }
}
