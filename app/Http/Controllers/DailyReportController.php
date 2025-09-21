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
            'work_date' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:6144'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('daily-reports', 'public');
        }

        DailyReport::create([
            'student_user_id' => Auth::id(),
            'work_date' => $request->date('work_date'),
            'summary' => $request->string('summary'),
            'attachment_path' => $path,
        ]);

        return redirect()->route('reports.index')->with('success', 'Daily report submitted.');
    }
}


