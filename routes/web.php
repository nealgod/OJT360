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

    // Placement requests (students)
    Route::get('/placements', [App\Http\Controllers\PlacementRequestController::class, 'index'])->name('placements.index');
    Route::get('/placements/create', [App\Http\Controllers\PlacementRequestController::class, 'create'])->name('placements.create');
    Route::post('/placements', [App\Http\Controllers\PlacementRequestController::class, 'store'])->name('placements.store');

    // Attendance & Reports (students)
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/time-in', [App\Http\Controllers\AttendanceController::class, 'timeIn'])->name('attendance.timeIn');
    Route::post('/attendance/time-out', [App\Http\Controllers\AttendanceController::class, 'timeOut'])->name('attendance.timeOut');
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
    Route::delete('/coord/companies/{company}', [App\Http\Controllers\CompanyController::class, 'destroy'])->name('coord.companies.destroy');
});
