<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by user role (admin enhancement)
        if ($request->filled('role')) {
            $role = $request->role;
            $query->whereHas('user', function($q) use ($role) {
                $q->where('role', $role);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get filter options
        $users = User::orderBy('name')->get();
        $actions = AuditLog::distinct()->pluck('action');
        $modelTypes = AuditLog::distinct()->whereNotNull('model_type')->pluck('model_type');

        return view('admin.audit.index', compact('logs', 'users', 'actions', 'modelTypes'));
    }

    public function show(AuditLog $audit)
    {
        $audit->load('user');
        return view('admin.audit.show', compact('audit'));
    }
}
