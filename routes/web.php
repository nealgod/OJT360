<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'profile.complete', 'force.password.change'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Force first password change routes (accessible even when forced)
    Route::get('/password/first-change', [App\Http\Controllers\PasswordController::class, 'showFirstChange'])->name('password.first-change');
    Route::post('/password/first-change', [App\Http\Controllers\PasswordController::class, 'updateFirstChange'])->name('password.first-change.update');

    // Protected routes (subject to force password change)
});

Route::middleware(['auth', 'force.password.change', 'profile.complete'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Company routes
    Route::get('/companies', [App\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');
    
    // Notification routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/create', [App\Http\Controllers\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [App\Http\Controllers\NotificationController::class, 'store'])->name('notifications.store');
    Route::patch('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Messaging routes
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [App\Http\Controllers\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::patch('/messages/{message}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.read');
    Route::patch('/messages/{message}/unread', [App\Http\Controllers\MessageController::class, 'markAsUnread'])->name('messages.unread');
    Route::delete('/messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');

    // Placement requests (students)
    Route::get('/placements', [App\Http\Controllers\PlacementRequestController::class, 'index'])->name('placements.index');
    Route::get('/placements/create', [App\Http\Controllers\PlacementRequestController::class, 'create'])->name('placements.create');
    Route::post('/placements', [App\Http\Controllers\PlacementRequestController::class, 'store'])->name('placements.store');
    Route::post('/placements/{placementRequest}/dismiss', [App\Http\Controllers\PlacementRequestController::class, 'dismiss'])->name('placements.dismiss');
    Route::get('/placements/my', [App\Http\Controllers\PlacementRequestController::class, 'myPlacement'])->name('placements.my');
    Route::post('/placements/{placementRequest}/propose-supervisor', [App\Http\Controllers\PlacementRequestController::class, 'proposeSupervisor'])->name('placements.propose-supervisor');

    // Attendance & Reports (students)
    Route::middleware(['placement.started'])->group(function () {
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/time-in', [App\Http\Controllers\AttendanceController::class, 'timeIn'])->name('attendance.timeIn');
        Route::post('/attendance/time-out', [App\Http\Controllers\AttendanceController::class, 'timeOut'])->name('attendance.timeOut');
        Route::post('/attendance/recovery', [App\Http\Controllers\AttendanceController::class, 'recovery'])->name('attendance.recovery');
    });
    
    // (Removed enhanced/isolated attendance routes to keep original flow only)
    Route::middleware(['placement.started'])->group(function () {
        Route::get('/reports', [App\Http\Controllers\DailyReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [App\Http\Controllers\DailyReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [App\Http\Controllers\DailyReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [App\Http\Controllers\DailyReportController::class, 'show'])->name('reports.show');
        Route::delete('/reports/{report}', [App\Http\Controllers\DailyReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('/reports/weekly/generate', [App\Http\Controllers\DailyReportController::class, 'weekly'])->name('reports.weekly');
        Route::post('/reports/weekly/generate', [App\Http\Controllers\DailyReportController::class, 'generateWeekly'])->name('reports.generate-weekly');
        Route::post('/reports/weekly/download', [App\Http\Controllers\DailyReportController::class, 'downloadWeekly'])->name('reports.download-weekly');
        Route::post('/reports/weekly/submit', [App\Http\Controllers\DailyReportController::class, 'submitWeeklyToDocuments'])->name('reports.submit-weekly');
    });
    
    // Document Requirements
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{requirement}', [App\Http\Controllers\DocumentController::class, 'show'])->name('documents.show');
    Route::post('/documents/{requirement}/submit', [App\Http\Controllers\DocumentController::class, 'submit'])->name('documents.submit');
    Route::delete('/documents/submissions/{submission}/cancel', [App\Http\Controllers\DocumentController::class, 'cancel'])->name('documents.cancel');
    Route::get('/documents/submissions/{submission}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/submissions/{submission}/preview', [App\Http\Controllers\DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/submissions/{submission}/stream', [App\Http\Controllers\DocumentController::class, 'stream'])->name('documents.stream');
});

// Admin routes
Route::middleware(['auth', 'verified', 'force.password.change', 'profile.complete'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
    });
});

require __DIR__.'/auth.php';

// Claim-your-account routes
Route::get('/activate', [App\Http\Controllers\ActivationController::class, 'show'])->name('activate.show');
Route::post('/activate', [App\Http\Controllers\ActivationController::class, 'activate'])->name('activate');

// Coordinator placement inbox
Route::middleware(['auth', 'verified', 'force.password.change', 'profile.complete'])->group(function () {
    Route::get('/coord/placements', [App\Http\Controllers\PlacementRequestController::class, 'inbox'])->name('coord.placements.inbox');
    Route::post('/coord/placements/{placementRequest}/approve', [App\Http\Controllers\PlacementRequestController::class, 'approve'])->name('coord.placements.approve');
    Route::post('/coord/placements/{placementRequest}/decline', [App\Http\Controllers\PlacementRequestController::class, 'decline'])->name('coord.placements.decline');
    Route::get('/coord/placements/{placementRequest}/supervisors', [App\Http\Controllers\PlacementRequestController::class, 'getSupervisors'])->name('coord.placements.supervisors');

    // Coordinator manage companies
    Route::get('/coord/companies/create', [App\Http\Controllers\CompanyController::class, 'create'])->name('coord.companies.create');
    Route::post('/coord/companies', [App\Http\Controllers\CompanyController::class, 'store'])->name('coord.companies.store');
    Route::get('/coord/companies/{company}/edit', [App\Http\Controllers\CompanyController::class, 'edit'])->name('coord.companies.edit');
    Route::post('/coord/companies/{company}', [App\Http\Controllers\CompanyController::class, 'update'])->name('coord.companies.update');
    Route::patch('/coord/companies/{company}/toggle-status', [App\Http\Controllers\CompanyController::class, 'toggleStatus'])->name('coord.companies.toggle-status');
    Route::delete('/coord/companies/{company}', [App\Http\Controllers\CompanyController::class, 'destroy'])->name('coord.companies.destroy');

    // Coordinator manage supervisors
    Route::get('/coord/supervisors', [App\Http\Controllers\CoordinatorSupervisorController::class, 'index'])->name('coord.supervisors.index');
    Route::get('/coord/supervisors/create', [App\Http\Controllers\CoordinatorSupervisorController::class, 'create'])->name('coord.supervisors.create');
    Route::post('/coord/supervisors', [App\Http\Controllers\CoordinatorSupervisorController::class, 'store'])->name('coord.supervisors.store');
    
    // Coordinator manage program hours
    Route::get('/coord/program/hours', [App\Http\Controllers\CoordinatorProgramController::class, 'showHours'])->name('coord.program.hours');
    Route::patch('/coord/program/hours', [App\Http\Controllers\CoordinatorProgramController::class, 'updateHours'])->name('coord.program.update-hours');

    // Coordinator manage students
    Route::get('/coord/students', [App\Http\Controllers\CoordinatorStudentController::class, 'index'])->name('coord.students.index');

    // Coordinator whitelist import (static routes must be BEFORE the {student} param route)
    Route::get('/coord/students/import', [App\Http\Controllers\CoordinatorImportController::class, 'showImport'])->name('coord.students.import');
    Route::post('/coord/students/import/preview', [App\Http\Controllers\CoordinatorImportController::class, 'preview'])->name('coord.students.import.preview');
    Route::post('/coord/students/import/commit', [App\Http\Controllers\CoordinatorImportController::class, 'commit'])->name('coord.students.import.commit');
    Route::get('/coord/students/whitelist', [App\Http\Controllers\CoordinatorImportController::class, 'status'])->name('coord.students.whitelist');
    Route::get('/coord/students/whitelist/export', [App\Http\Controllers\CoordinatorImportController::class, 'export'])->name('coord.students.whitelist.export');
    Route::get('/coord/students/whitelist/uploaded-file', [App\Http\Controllers\CoordinatorImportController::class, 'downloadUploaded'])->name('coord.students.whitelist.uploaded');
    Route::post('/coord/students/whitelist/end-term', [App\Http\Controllers\CoordinatorImportController::class, 'endTerm'])->name('coord.students.whitelist.end-term');

    Route::get('/coord/students/{student}', [App\Http\Controllers\CoordinatorStudentController::class, 'show'])->name('coord.students.show');
    Route::post('/coord/students/{student}/update-company', [App\Http\Controllers\CoordinatorStudentController::class, 'updateCompany'])->name('coord.students.update-company');
    Route::post('/coord/students/{student}/update-status', [App\Http\Controllers\CoordinatorStudentController::class, 'updateStatus'])->name('coord.students.update-status');
    Route::post('/coord/students/{student}/assign-supervisor', [App\Http\Controllers\CoordinatorStudentController::class, 'assignSupervisor'])->name('coord.students.assign-supervisor');
    
    // Coordinator document review
    Route::get('/coord/documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('coord.documents.index');
    Route::post('/coord/documents/submissions/{submission}/review', [App\Http\Controllers\DocumentController::class, 'review'])->name('coord.documents.review');
    Route::post('/coord/documents/bulk-review', [App\Http\Controllers\DocumentController::class, 'bulkReview'])->name('coord.documents.bulk-review');

    // API route for fetching attendance data by date
    Route::get('/api/attendance/{date}', function (Request $request, $date) {
        try {
            $user = Auth::user();
            
            // Only allow students
            if (!$user->isStudent()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            // Validate date format
            $dateObj = \Carbon\Carbon::parse($date);
            
            // Get attendance for the selected date
            $attendance = \App\Models\AttendanceLog::where('student_user_id', $user->id)
                ->whereDate('work_date', $dateObj->format('Y-m-d'))
                ->first();
            
            if ($attendance) {
                return response()->json([
                    'success' => true,
                    'attendance' => [
                        'time_in' => $attendance->time_in,
                        'time_out' => $attendance->time_out,
                        'time_in_formatted' => $attendance->time_in_formatted,
                        'time_out_formatted' => $attendance->time_out_formatted,
                        'hours_worked_formatted' => $attendance->hours_worked_formatted,
                        'minutes_worked' => $attendance->minutes_worked,
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No attendance found for this date'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format'
            ], 400);
        }
    })->name('api.attendance');
});
