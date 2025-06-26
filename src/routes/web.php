<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagementController;

Route::middleware('auth')->group(function () {
    Route::get('/attendance', [ManagementController::class, 'showAttendanceForm'])->name('attendance.form');
    Route::post('/attendance', [ManagementController::class, 'startAttendance'])->name('attendance.start');
    Route::post('/attendance/break', [ManagementController::class, 'takeBreak'])->name('attendance.break');
    Route::post('/attendance/return', [ManagementController::class, 'returnFromBreak'])->name('attendance.return');
    Route::post('/attendance/leave', [ManagementController::class, 'endAttendance'])->name('attendance.leave');

    Route::get('/attendance/list', [ManagementController::class, 'showAttendanceList'])->name('attendance.list');
    Route::get('/attendance/{id}', [ManagementController::class, 'showAttendanceDetail'])->name('attendance.detail');
    Route::post('/attendance/{id}', [ManagementController::class, 'updateAttendance'])->name('attendance.update');

    Route::post('/correction/request/{id}', [ManagementController::class, 'submitCorrectionRequest'])->name('correction.request.submit');
    Route::get('/stamp_correction_request/list', [ManagementController::class, 'showUserCorrectionList'])->name('correction.user.list');
});

Route::get('/admin/login', [ManagementController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [ManagementController::class, 'handleAdminLogin'])->name('admin.login.submit');

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin/attendance/list', [ManagementController::class, 'showAdminAttendanceList'])->name('admin.attendance.list');
    Route::get('/admin/attendance/{id}', [ManagementController::class, 'showAdminAttendanceDetail'])->name('admin.attendance.detail');

    Route::get('/admin/staff/list', [ManagementController::class, 'showStaffList'])->name('admin.staff.list');
    Route::get('/admin/staff/{id}/attendance', [ManagementController::class, 'showStaffAttendance'])->name('admin.staff.attendance');
    Route::get('/admin/staff/{id}/attendance/export', [ManagementController::class, 'exportStaffAttendance'])->name('admin.staff.attendance.export');

    Route::get('/admin/stamp_correction_request/list', [ManagementController::class, 'showAdminCorrectionList'])->name('correction.admin.list');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [ManagementController::class, 'showCorrectionRequestDetail'])->name('correction.approve.show');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request}', [ManagementController::class, 'approveCorrectionRequest'])->name('correction.approve');
});
