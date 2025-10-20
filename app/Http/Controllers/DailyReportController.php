<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}


