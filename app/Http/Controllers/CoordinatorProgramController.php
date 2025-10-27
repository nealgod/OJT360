<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinatorProgramController extends Controller
{
    public function showHours()
    {
        $coordinator = Auth::user();
        $program = $coordinator->coordinatorProfile?->program;
        
        if (!$program) {
            return redirect()->route('coord.students.index')->with('error', 'No program assigned to you.');
        }

        // Get program with department
        $program = Program::with('department')->findOrFail($program->id);
        
        // Count students
        $totalStudents = \App\Models\User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($program) {
                $q->where('course', $program->name);
            })
            ->count();
            
        $studentsWithCustomHours = \App\Models\User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($program) {
                $q->where('course', $program->name)
                  ->whereNotNull('required_hours');
            })
            ->count();
        
        $studentsUsingDefault = $totalStudents - $studentsWithCustomHours;

        return view('coord.program.hours', compact('program', 'totalStudents', 'studentsWithCustomHours', 'studentsUsingDefault'));
    }

    public function updateHours(Request $request)
    {
        $coordinator = Auth::user();
        $program = $coordinator->coordinatorProfile?->program;
        
        if (!$program) {
            return back()->with('error', 'No program assigned to you.');
        }

        $request->validate([
            'required_hours' => ['required', 'integer', 'min:200', 'max:1000'],
        ]);

        $program->update([
            'required_hours' => $request->required_hours,
        ]);

        // Notify all students in this program (who don't have custom hours)
        $students = \App\Models\User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($program) {
                $q->where('course', $program->name)
                  ->whereNull('required_hours');
            })
            ->get();

        foreach ($students as $student) {
            \App\Models\Notification::create([
                'user_id' => $student->id,
                'type' => 'program_hours_updated',
                'title' => 'OJT Required Hours Updated',
                'message' => 'Your program\'s required OJT hours have been updated to ' . $request->required_hours . ' hours.',
                'data' => [
                    'program_id' => $program->id,
                    'required_hours' => $request->required_hours,
                ],
            ]);
        }

        return back()->with('success', 'Program required hours updated successfully.');
    }
}
