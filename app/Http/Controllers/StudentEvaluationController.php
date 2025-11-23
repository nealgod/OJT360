<?php

namespace App\Http\Controllers;

use App\Models\MonthlyEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentEvaluationController extends Controller
{
    public function index()
    {
        // Get evaluations for the current student (status only, no details)
        $evaluations = MonthlyEvaluation::forStudent(Auth::id())
            ->select([
                'id',
                'evaluation_month',
                'evaluation_year', 
                'month_number',
                'status',
                'submitted_at',
                'reviewed_at',
                'supervisor_name'
            ])
            ->orderByDesc('evaluation_year')
            ->orderByDesc('evaluation_month')
            ->get();

        return view('evaluations.index', compact('evaluations'));
    }
}
