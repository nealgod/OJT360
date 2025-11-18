<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class StudentPlacementController extends Controller
{
    /**
     * Show student's placement details from acceptance letter
     */
    public function show()
    {
        $user = Auth::user();
        abort_unless($user && $user->isStudent(), 403);

        $profile = $user->studentProfile;
        
        $acceptance = \App\Models\AcceptanceLetter::where('student_user_id', $user->id)
            ->latest()
            ->first();

        $company = $profile?->company ?? $acceptance?->company;
        $supervisor = $profile?->supervisor;

        return view('students.placement', compact(
            'profile',
            'acceptance',
            'company',
            'supervisor'
        ));
    }
}
