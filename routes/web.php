<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// PHMS Custom Routes
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\MedicationScheduleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SymptomController;

// Authenticated and verified routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lab Reports
    Route::prefix('labs')->name('labs.')->group(function () {
        Route::get('/', [LabController::class, 'index'])->name('index');
        Route::get('/{report}', [LabController::class, 'show'])->name('show');
        Route::post('/upload', [LabController::class, 'upload'])->name('upload');
        Route::post('/{report}/parse', [LabController::class, 'parse'])->name('parse');
    });

    // Symptoms
    Route::prefix('symptoms')->name('symptoms.')->group(function () {
        Route::get('/', [SymptomController::class, 'index'])->name('index');
        Route::post('/', [SymptomController::class, 'store'])->name('store');
        Route::delete('/{symptom}', [SymptomController::class, 'destroy'])->name('destroy');
    });

    // Medications
    Route::prefix('medications')->name('medications.')->group(function () {
        Route::get('/', [MedicationController::class, 'index'])->name('index');
        Route::post('/', [MedicationController::class, 'store'])->name('store');
        Route::put('/{medication}', [MedicationController::class, 'update'])->name('update');
        Route::delete('/{medication}', [MedicationController::class, 'destroy'])->name('destroy');

        // Medication Schedules
        Route::post('/{medication}/schedules', [MedicationScheduleController::class, 'store'])->name('schedules.store');
    });

    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::put('/{schedule}', [MedicationScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [MedicationScheduleController::class, 'destroy'])->name('destroy');
    });

    // Import
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::post('/upload', [ImportController::class, 'upload'])->name('upload');
    });

    // Export
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::post('/run', [ExportController::class, 'run'])->name('run');
        Route::get('/{export}/download', [ExportController::class, 'download'])->name('download');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/consent', [SettingsController::class, 'toggleConsent'])->name('consent');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('can:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });
});
