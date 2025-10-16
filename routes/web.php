<?php

use App\Http\Controllers\ProfileController;
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

    // Attendance & Reports (students)
    Route::middleware(['placement.started'])->group(function () {
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/time-in', [App\Http\Controllers\AttendanceController::class, 'timeIn'])->name('attendance.timeIn');
        Route::post('/attendance/time-out', [App\Http\Controllers\AttendanceController::class, 'timeOut'])->name('attendance.timeOut');
        Route::post('/attendance/recovery', [App\Http\Controllers\AttendanceController::class, 'recovery'])->name('attendance.recovery');
    });
    
    // (Removed enhanced/isolated attendance routes to keep original flow only)
    Route::get('/reports', [App\Http\Controllers\DailyReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [App\Http\Controllers\DailyReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [App\Http\Controllers\DailyReportController::class, 'store'])->name('reports.store');
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

// Coordinator placement inbox
Route::middleware(['auth', 'verified', 'force.password.change', 'profile.complete'])->group(function () {
    Route::get('/coord/placements', [App\Http\Controllers\PlacementRequestController::class, 'inbox'])->name('coord.placements.inbox');
    Route::post('/coord/placements/{placementRequest}/approve', [App\Http\Controllers\PlacementRequestController::class, 'approve'])->name('coord.placements.approve');
    Route::post('/coord/placements/{placementRequest}/decline', [App\Http\Controllers\PlacementRequestController::class, 'decline'])->name('coord.placements.decline');

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

    // Coordinator manage students
    Route::get('/coord/students', [App\Http\Controllers\CoordinatorStudentController::class, 'index'])->name('coord.students.index');
    Route::get('/coord/students/{student}', [App\Http\Controllers\CoordinatorStudentController::class, 'show'])->name('coord.students.show');
    Route::post('/coord/students/{student}/update-company', [App\Http\Controllers\CoordinatorStudentController::class, 'updateCompany'])->name('coord.students.update-company');
    Route::post('/coord/students/{student}/update-status', [App\Http\Controllers\CoordinatorStudentController::class, 'updateStatus'])->name('coord.students.update-status');
});
