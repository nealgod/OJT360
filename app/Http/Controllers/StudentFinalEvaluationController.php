<?php

namespace App\Http\Controllers;

use App\Models\FinalEvaluation;
use Illuminate\Support\Facades\Auth;

class StudentFinalEvaluationController extends Controller
{
    public function status()
    {
        // Get final evaluation for current student (if exists)
        $evaluation = FinalEvaluation::forStudent(Auth::id())->first();

        return view('evaluations.final-status', compact('evaluation'));
    }
}
