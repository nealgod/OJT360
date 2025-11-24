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

// Supervisor Registration Routes (Public)
Route::prefix('register/supervisor')->name('supervisor.register')->group(function () {
    Route::get('/', [App\Http\Controllers\SupervisorRegistrationController::class, 'showEmailForm']);
    Route::post('/send', [App\Http\Controllers\SupervisorRegistrationController::class, 'sendVerification'])->name('.send');
    Route::get('/verify/{token}', [App\Http\Controllers\SupervisorRegistrationController::class, 'verify'])->name('.verify');
    Route::post('/complete', [App\Http\Controllers\SupervisorRegistrationController::class, 'complete'])->name('.complete');
    Route::post('/resend', [App\Http\Controllers\SupervisorRegistrationController::class, 'resendVerification'])->name('.resend');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'profile.complete'])->name('dashboard');

Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Company routes
    Route::get('/companies', [App\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');
    
    // Notification routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Messaging routes
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [App\Http\Controllers\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::patch('/messages/{message}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.read');
    Route::patch('/messages/{message}/unread', [App\Http\Controllers\MessageController::class, 'markAsUnread'])->name('messages.unread');
    Route::delete('/messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');

    // Weekly Reports (always accessible for students)
    Route::prefix('reports/weekly')->name('reports.weekly.')->group(function () {
        Route::get('/', [App\Http\Controllers\WeeklyReportController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\WeeklyReportController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\WeeklyReportController::class, 'store'])->name('store');
        Route::get('/{weekly}', [App\Http\Controllers\WeeklyReportController::class, 'show'])->name('show');
        Route::patch('/{weekly}/submit', [App\Http\Controllers\WeeklyReportController::class, 'submit'])->name('submit');
        Route::delete('/{weekly}', [App\Http\Controllers\WeeklyReportController::class, 'destroy'])->name('destroy');
        Route::get('/{weekly}/pdf', [App\Http\Controllers\WeeklyReportController::class, 'downloadPdf'])->name('pdf');
    });
    
    // Attendance & Reports (students)
    Route::middleware(['placement.started'])->group(function () {
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/time-in', [App\Http\Controllers\AttendanceController::class, 'timeIn'])->name('attendance.timeIn');
        Route::post('/attendance/time-out', [App\Http\Controllers\AttendanceController::class, 'timeOut'])->name('attendance.timeOut');
        Route::post('/attendance/recovery', [App\Http\Controllers\AttendanceController::class, 'recovery'])->name('attendance.recovery');
        Route::post('/attendance/report-absence', [App\Http\Controllers\AttendanceController::class, 'reportAbsence'])->name('attendance.reportAbsence');
    });
    
    // Monthly Evaluations (status only for students)
    Route::get('/evaluations', [App\Http\Controllers\StudentEvaluationController::class, 'index'])->name('evaluations.index');
    
    // Final Evaluation Status (status only for students)
    Route::get('/evaluations/final/status', [App\Http\Controllers\StudentFinalEvaluationController::class, 'status'])->name('evaluations.final.status');
    
    // Daily reports removed - now using weekly reports only
    
    // Document Requirements
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{requirement}', [App\Http\Controllers\DocumentController::class, 'show'])->name('documents.show');
    Route::post('/documents/{requirement}/submit', [App\Http\Controllers\DocumentController::class, 'submit'])->name('documents.submit');
    Route::delete('/documents/submissions/{submission}/cancel', [App\Http\Controllers\DocumentController::class, 'cancel'])->name('documents.cancel');
    Route::get('/documents/submissions/{submission}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/submissions/{submission}/stream', [App\Http\Controllers\DocumentController::class, 'stream'])->name('documents.stream');
    
    // Acceptance Letter Download (auth required)
    Route::get('/acceptance-letters/{letter}/download', [App\Http\Controllers\AcceptanceLetterController::class, 'download'])->name('acceptance-letters.download');
    
    // Student Documents (Resume & Application Letter)
    Route::get('/student-documents', [App\Http\Controllers\StudentDocumentController::class, 'index'])->name('student-documents.index');
    
    // Resume routes
    Route::get('/student-documents/resume/create', [App\Http\Controllers\StudentDocumentController::class, 'createResume'])->name('student-documents.resume.create');
    Route::post('/student-documents/resume', [App\Http\Controllers\StudentDocumentController::class, 'storeResume'])->name('student-documents.resume.store');
    Route::get('/student-documents/resume/{resume}/edit', [App\Http\Controllers\StudentDocumentController::class, 'editResume'])->name('student-documents.resume.edit');
    Route::patch('/student-documents/resume/{resume}', [App\Http\Controllers\StudentDocumentController::class, 'updateResume'])->name('student-documents.resume.update');
    Route::delete('/student-documents/resume/{resume}', [App\Http\Controllers\StudentDocumentController::class, 'destroyResume'])->name('student-documents.resume.destroy');
    Route::get('/student-documents/resume/{resume}/download', [App\Http\Controllers\StudentDocumentController::class, 'downloadResume'])->name('student-documents.resume.download');
    
    // Application Letter routes
    Route::get('/student-documents/application-letter/create', [App\Http\Controllers\StudentDocumentController::class, 'createApplicationLetter'])->name('student-documents.application-letter.create');
    Route::post('/student-documents/application-letter', [App\Http\Controllers\StudentDocumentController::class, 'storeApplicationLetter'])->name('student-documents.application-letter.store');
    Route::get('/student-documents/application-letter/{letter}/edit', [App\Http\Controllers\StudentDocumentController::class, 'editApplicationLetter'])->name('student-documents.application-letter.edit');
    Route::patch('/student-documents/application-letter/{letter}', [App\Http\Controllers\StudentDocumentController::class, 'updateApplicationLetter'])->name('student-documents.application-letter.update');
    Route::delete('/student-documents/application-letter/{letter}', [App\Http\Controllers\StudentDocumentController::class, 'destroyApplicationLetter'])->name('student-documents.application-letter.destroy');
    Route::get('/student-documents/application-letter/{letter}/download', [App\Http\Controllers\StudentDocumentController::class, 'downloadApplicationLetter'])->name('student-documents.application-letter.download');
    
    // Submit documents to coordinator
    Route::post('/student-documents/resume/{resume}/submit', [App\Http\Controllers\StudentDocumentController::class, 'submitResume'])->name('student-documents.resume.submit');
    Route::post('/student-documents/application-letter/{letter}/submit', [App\Http\Controllers\StudentDocumentController::class, 'submitApplicationLetter'])->name('student-documents.application-letter.submit');
    
    // Legacy resume routes (redirect to new routes for backward compatibility)
    Route::get('/resume', function() { return redirect()->route('student-documents.index'); });
    Route::get('/resume/create', function() { return redirect()->route('student-documents.resume.create'); });
});

// Admin routes
Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
        
        // Departments & Programs
        Route::get('/departments', [App\Http\Controllers\AdminDepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [App\Http\Controllers\AdminDepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [App\Http\Controllers\AdminDepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [App\Http\Controllers\AdminDepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::post('/departments/{department}/programs', [App\Http\Controllers\AdminDepartmentController::class, 'storeProgram'])->name('departments.programs.store');
        Route::put('/programs/{program}', [App\Http\Controllers\AdminDepartmentController::class, 'updateProgram'])->name('programs.update');
        Route::delete('/programs/{program}', [App\Http\Controllers\AdminDepartmentController::class, 'destroyProgram'])->name('programs.destroy');
        
        // Reports & Analytics
        Route::get('/reports', [App\Http\Controllers\AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/attendance', [App\Http\Controllers\AdminReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/weekly', [App\Http\Controllers\AdminReportController::class, 'weeklyReports'])->name('reports.weekly');
        Route::get('/reports/evaluations', [App\Http\Controllers\AdminReportController::class, 'evaluations'])->name('reports.evaluations');
        
        // Audit Logs
        Route::get('/audit', [App\Http\Controllers\AdminAuditController::class, 'index'])->name('audit.index');
        Route::get('/audit/{audit}', [App\Http\Controllers\AdminAuditController::class, 'show'])->name('audit.show');
    });
});

require __DIR__.'/auth.php';

// Student registration (two-step: verify ID -> complete registration)
Route::get('/register/student', [App\Http\Controllers\ActivationController::class, 'showVerifyId'])->name('student.verify-id');
Route::post('/register/student', [App\Http\Controllers\ActivationController::class, 'sendVerification'])->name('student.send-verification');
Route::get('/register/student/complete/{token}', [App\Http\Controllers\ActivationController::class, 'showComplete'])->name('student.complete.show');
Route::post('/register/student/complete', [App\Http\Controllers\ActivationController::class, 'completeRegistration'])->name('student.complete');

// Coordinator invitation (admin initiated -> email link -> complete)
Route::get('/register/coordinator/complete/{token}', [App\Http\Controllers\ActivationController::class, 'showCompleteCoordinator'])->name('coordinator.complete.show');
Route::post('/register/coordinator/complete', [App\Http\Controllers\ActivationController::class, 'completeCoordinator'])->name('coordinator.complete');
Route::post('/register/coordinator/resend', [App\Http\Controllers\ActivationController::class, 'resendCoordinatorInvite'])->name('coordinator.invite.resend');

// Legacy claim-your-account (kept temporarily; will be hidden in UI)
Route::get('/activate', [App\Http\Controllers\ActivationController::class, 'show'])->name('activate.show');
Route::post('/activate', [App\Http\Controllers\ActivationController::class, 'activate'])->name('activate');

Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::get('/my-placement', [App\Http\Controllers\StudentPlacementController::class, 'show'])->name('student.placement.show');
});

// Supervisor Routes (auth required)
Route::middleware(['auth'])->group(function () {
    // Acceptance Letters Management
    Route::get('/supervisor/acceptance-letters', [App\Http\Controllers\SupervisorAcceptanceController::class, 'index'])->name('supervisor.acceptance.index');
    Route::get('/supervisor/students', [App\Http\Controllers\SupervisorAcceptanceController::class, 'students'])->name('supervisor.students');
    
    // Student Search & Direct Acceptance (NEW FLOW)
    Route::get('/supervisor/students/search', [App\Http\Controllers\SupervisorAcceptanceController::class, 'searchForm'])->name('supervisor.students.search');
    Route::get('/api/supervisor/students/autocomplete', [App\Http\Controllers\SupervisorAcceptanceController::class, 'autocomplete'])->name('supervisor.students.autocomplete');
    Route::post('/supervisor/students/search', [App\Http\Controllers\SupervisorAcceptanceController::class, 'search'])->name('supervisor.students.search.post');
    Route::get('/supervisor/students/{student}', [App\Http\Controllers\SupervisorAcceptanceController::class, 'viewStudent'])->name('supervisor.students.view');
    Route::get('/supervisor/students/{student}/accept', [App\Http\Controllers\SupervisorAcceptanceController::class, 'acceptStudent'])->name('supervisor.students.accept');
    Route::post('/supervisor/students/{student}/generate-letter', [App\Http\Controllers\SupervisorAcceptanceController::class, 'generateLetter'])->name('supervisor.students.generate');
    Route::get('/supervisor/students/success/{letter}', [App\Http\Controllers\SupervisorAcceptanceController::class, 'showSuccess'])->name('supervisor.students.success');
    
    // Supervisor Monthly Evaluations
    Route::prefix('supervisor/evaluations')->name('supervisor.evaluations.')->group(function () {
        Route::get('/', [App\Http\Controllers\SupervisorEvaluationController::class, 'index'])->name('index');
        Route::get('/create/{student}', [App\Http\Controllers\SupervisorEvaluationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SupervisorEvaluationController::class, 'store'])->name('store');
        Route::get('/{evaluation}', [App\Http\Controllers\SupervisorEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluation}/pdf', [App\Http\Controllers\SupervisorEvaluationController::class, 'downloadPdf'])->name('pdf');
    });
    
    // Supervisor Final Evaluations
    Route::prefix('supervisor/final-evaluations')->name('supervisor.final-evaluations.')->group(function () {
        Route::get('/', [App\Http\Controllers\SupervisorFinalEvaluationController::class, 'index'])->name('index');
        Route::get('/create/{student}', [App\Http\Controllers\SupervisorFinalEvaluationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SupervisorFinalEvaluationController::class, 'store'])->name('store');
        Route::get('/{evaluation}', [App\Http\Controllers\SupervisorFinalEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluation}/pdf', [App\Http\Controllers\SupervisorFinalEvaluationController::class, 'downloadPdf'])->name('pdf');
    });
});

// Coordinator placement inbox
Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
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

    // Coordinator view weekly reports
    Route::get('/coord/reports', [App\Http\Controllers\CoordinatorReportController::class, 'index'])->name('coord.reports.index');
    Route::get('/coord/reports/{report}', [App\Http\Controllers\CoordinatorReportController::class, 'show'])->name('coord.reports.show');
    Route::get('/coord/reports/{report}/pdf', [App\Http\Controllers\CoordinatorReportController::class, 'downloadPdf'])->name('coord.reports.pdf');
    Route::patch('/coord/reports/{report}/status', [App\Http\Controllers\CoordinatorReportController::class, 'updateStatus'])->name('coord.reports.update-status');

    // Coordinator view monthly evaluations
    Route::prefix('coord/evaluations')->name('coordinator.evaluations.')->group(function () {
        Route::get('/', [App\Http\Controllers\CoordinatorEvaluationController::class, 'index'])->name('index');
        Route::get('/{evaluation}', [App\Http\Controllers\CoordinatorEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluation}/pdf', [App\Http\Controllers\CoordinatorEvaluationController::class, 'downloadPdf'])->name('download-pdf');
        Route::patch('/{evaluation}/review', [App\Http\Controllers\CoordinatorEvaluationController::class, 'markReviewed'])->name('mark-reviewed');
    });
    
    // Coordinator view final evaluations
    Route::prefix('coord/final-evaluations')->name('coordinator.final-evaluations.')->group(function () {
        Route::get('/', [App\Http\Controllers\CoordinatorFinalEvaluationController::class, 'index'])->name('index');
        Route::get('/{evaluation}', [App\Http\Controllers\CoordinatorFinalEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluation}/pdf', [App\Http\Controllers\CoordinatorFinalEvaluationController::class, 'downloadPdf'])->name('download-pdf');
        Route::patch('/{evaluation}/review', [App\Http\Controllers\CoordinatorFinalEvaluationController::class, 'markReviewed'])->name('mark-reviewed');
    });

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
    
    // Coordinator attendance recovery approval
    Route::post('/coord/attendance/{log}/approve-recovery', [App\Http\Controllers\CoordinatorAttendanceController::class, 'approveRecovery'])->name('coord.attendance.approve-recovery');
    Route::post('/coord/attendance/{log}/reject-recovery', [App\Http\Controllers\CoordinatorAttendanceController::class, 'rejectRecovery'])->name('coord.attendance.reject-recovery');
    
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
