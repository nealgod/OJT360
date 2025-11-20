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
        
        // Count students in this program
        $totalStudents = \App\Models\User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($program) {
                $q->where('course', $program->name);
            })
            ->count();

        return view('coord.program.hours', compact('program', 'totalStudents'));
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

        $oldHours = $program->required_hours;
        $newHours = $request->required_hours;

        // Check if hours actually changed
        if ($oldHours == $newHours) {
            return back()->with('info', 'No changes made. The required hours are already set to ' . $newHours . ' hours.');
        }

        // Update program hours
        $program->update([
            'required_hours' => $newHours,
        ]);

        // Get students who will be affected (those without custom hours)
        $affectedStudents = \App\Models\User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($program) {
                $q->where('course', $program->name)
                  ->whereNull('required_hours');
            })
            ->get();

        $affectedCount = $affectedStudents->count();

        // Notify all affected students
        foreach ($affectedStudents as $student) {
            \App\Models\Notification::create([
                'user_id' => $student->id,
                'type' => 'program_hours_updated',
                'title' => 'OJT Required Hours Updated',
                'message' => sprintf(
                    'Your program\'s required OJT hours have been updated from %s to %s hours by your coordinator.',
                    $oldHours ?? 'unset',
                    $newHours
                ),
                'data' => [
                    'program_id' => $program->id,
                    'old_hours' => $oldHours,
                    'new_hours' => $newHours,
                    'updated_by' => $coordinator->name,
                    'updated_at' => now()->toDateTimeString(),
                ],
            ]);
        }

        // Log the change
        \Log::info('Program hours updated', [
            'program_id' => $program->id,
            'program_name' => $program->name,
            'coordinator_id' => $coordinator->id,
            'coordinator_name' => $coordinator->name,
            'old_hours' => $oldHours,
            'new_hours' => $newHours,
            'affected_students' => $affectedCount,
        ]);

        $successMessage = sprintf(
            'Program required hours updated successfully from %s to %s hours. %s student(s) have been notified.',
            $oldHours ?? 'unset',
            $newHours,
            $affectedCount
        );

        return back()->with('success', $successMessage);
    }
}
