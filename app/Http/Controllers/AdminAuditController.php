<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function index(Request $request)
    {
        try {
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

            if ($request->filled('role')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('role', $request->role);
                });
            }

            // Filter by date range with validation
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Validate date range
            if ($request->filled('date_from') && $request->filled('date_to')) {
                if ($request->date_from > $request->date_to) {
                    return redirect()->route('admin.audit.index')
                        ->with('error', 'Start date must be before or equal to end date.')
                        ->withInput($request->except('date_from', 'date_to'));
                }
            }

            // Search in description
            if ($request->filled('search')) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }

            // Quick filter presets
            if ($request->filled('preset')) {
                switch ($request->preset) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                        break;
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $allowedSorts = ['created_at', 'action', 'model_type'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $logs = $query->paginate(20)->withQueryString();

            // Get filter options (optimized - limit users and cache)
            $users = User::orderBy('name')->limit(500)->get(['id', 'name']); // Limit to prevent huge dropdowns
            $actions = AuditLog::distinct()->orderBy('action')->pluck('action');
            
            // Filter model types to show only common/relevant ones
            $allModelTypes = AuditLog::distinct()->whereNotNull('model_type')->orderBy('model_type')->pluck('model_type');
            $modelTypes = $allModelTypes->filter(function($modelType) {
                // Only show models that are commonly used or user-facing
                $basename = class_basename($modelType);
                $relevantModels = [
                    'User', 'WeeklyReport', 'AttendanceLog', 'MonthlyEvaluation', 
                    'FinalEvaluation', 'Department', 'Program', 'StudentProfile',
                    'Supervisor', 'Coordinator', 'Acceptance'
                ];
                return in_array($basename, $relevantModels);
            });
            
            $roles = ['admin', 'coordinator', 'supervisor', 'intern'];

            // Get statistics
            $stats = [
                'total' => AuditLog::count(),
                'today' => AuditLog::whereDate('created_at', today())->count(),
                'this_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => AuditLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            ];

            return view('admin.audit.index', compact('logs', 'users', 'actions', 'modelTypes', 'roles', 'stats', 'sortBy', 'sortOrder'));
        } catch (\Exception $e) {
            \Log::error('Admin audit logs error: ' . $e->getMessage());
            
            // Return empty data with error message
            $users = collect();
            $actions = collect();
            $modelTypes = collect();
            $roles = ['admin', 'coordinator', 'supervisor', 'intern'];
            $stats = ['total' => 0, 'today' => 0, 'this_week' => 0, 'this_month' => 0];
            $logs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $sortBy = 'created_at';
            $sortOrder = 'desc';
            
            return view('admin.audit.index', compact('logs', 'users', 'actions', 'modelTypes', 'roles', 'stats', 'sortBy', 'sortOrder'))
                ->with('error', 'Unable to load audit logs. Please try again or adjust your filters.');
        }
    }

    public function show(AuditLog $audit)
    {
        $audit->load('user');

        // Get related logs for the same model (if applicable)
        $relatedLogs = collect();
        if ($audit->model_type && $audit->model_id) {
            $relatedLogs = AuditLog::where('model_type', $audit->model_type)
                ->where('model_id', $audit->model_id)
                ->where('id', '!=', $audit->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('admin.audit.show', compact('audit', 'relatedLogs'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('preset')) {
            switch ($request->preset) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'audit_logs_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            
            // BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, [
                'ID',
                'Timestamp',
                'User',
                'User Role',
                'Action',
                'Model Type',
                'Model ID',
                'Description',
                'IP Address',
                'User Agent'
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user?->name ?? 'System',
                    $log->user?->role ?? 'N/A',
                    $log->action,
                    $log->model_type ?? 'N/A',
                    $log->model_id ?? 'N/A',
                    $log->description,
                    $log->ip_address ?? 'N/A',
                    $log->user_agent ?? 'N/A',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function userActivity(User $user)
    {
        $logs = AuditLog::where('user_id', $user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total' => AuditLog::where('user_id', $user->id)->count(),
            'today' => AuditLog::where('user_id', $user->id)->whereDate('created_at', today())->count(),
            'this_week' => AuditLog::where('user_id', $user->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'by_action' => AuditLog::where('user_id', $user->id)
                ->selectRaw('action, count(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action'),
        ];

        return view('admin.audit.user-activity', compact('user', 'logs', 'stats'));
    }
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'log_ids' => 'required|array',
                'log_ids.*' => 'exists:audit_logs,id'
            ]);

            $count = AuditLog::whereIn('id', $request->log_ids)->delete();

            return redirect()->route('admin.audit.index')
                ->with('success', "Successfully deleted {$count} audit log(s).");
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Bulk delete audit logs error: ' . $e->getMessage());
            return redirect()->route('admin.audit.index')
                ->with('error', 'Failed to delete audit logs. Please try again.');
        }
    }

    public function deleteOlderThan(Request $request)
    {
        try {
            $request->validate([
                'days' => 'required|integer|min:1|max:365'
            ]);

            $date = now()->subDays($request->days);
            $count = AuditLog::where('created_at', '<', $date)->delete();

            return redirect()->route('admin.audit.index')
                ->with('success', "Successfully deleted {$count} audit log(s) older than {$request->days} days.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Delete old audit logs error: ' . $e->getMessage());
            return redirect()->route('admin.audit.index')
                ->with('error', 'Failed to delete old audit logs. Please try again.');
        }
    }
}
