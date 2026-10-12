<?php

use App\Http\Controllers\Admin\ClassRoomController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ScheduleSessionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ScanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Area siswa.
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan', [ScanController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('scan.store');
});

// Area admin.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('classes', ClassRoomController::class)
        ->parameters(['classes' => 'classRoom'])
        ->except('show');

    Route::get('students/import', [StudentImportController::class, 'create'])->name('students.import.create');
    Route::post('students/import', [StudentImportController::class, 'store'])->name('students.import.store');
    Route::get('students/import/template', [StudentImportController::class, 'template'])->name('students.import.template');

    Route::resource('students', StudentController::class)
        ->except('show');

    Route::get('schedules/{schedule}/sessions', [ScheduleController::class, 'sessions'])->name('schedules.sessions');
    Route::post('schedules/{schedule}/generate', [ScheduleSessionController::class, 'generate'])->name('schedules.generate');

    Route::resource('schedules', ScheduleController::class)
        ->parameters(['schedules' => 'schedule'])
        ->except('show');

    Route::get('sessions/{session}', [ScheduleSessionController::class, 'show'])->name('sessions.show');
    Route::get('sessions/{session}/print', [ScheduleSessionController::class, 'print'])->name('sessions.print');
    Route::post('sessions/{session}/regenerate', [ScheduleSessionController::class, 'regenerate'])->name('sessions.regenerate');

    // Laporan & absensi manual.
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/manual', [ReportController::class, 'manualForm'])->name('reports.manual.form');
    Route::post('reports/manual', [ReportController::class, 'manualStore'])->name('reports.manual.store');
    Route::get('reports/classes/{class}/excel', [ReportController::class, 'exportExcel'])->name('reports.class.excel');
    Route::get('reports/classes/{class}/pdf', [ReportController::class, 'exportPdf'])->name('reports.class.pdf');
    Route::get('reports/classes/{class}', [ReportController::class, 'classReport'])->name('reports.class');
    Route::get('reports/students/{student}', [ReportController::class, 'studentReport'])->name('reports.student');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
