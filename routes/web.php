 <?php

use App\Http\Controllers\Produksi\DeptProduksi\ActivityLogController;
use App\Http\Controllers\Produksi\DeptProduksi\ExportController;
use App\Http\Controllers\Produksi\DeptProduksi\SpreadsheetController;
use App\Http\Controllers\HRD\DeptHRD\KaryawanController;
use App\Http\Controllers\HRD\DeptHRD\KaryawanstaffController;
use App\Http\Controllers\HRD\DeptHRD\DepartemenController;
use App\Http\Controllers\HRD\DeptHRD\TrainingController;
use App\Http\Controllers\HRD\DeptHRD\OnboardingController;
use App\Http\Controllers\HRD\DeptHRD\AssetController;

Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
Route::get('/onboarding/tasks/{onboardingTask}', [OnboardingController::class, 'show'])->name('onboarding.tasks.show');
Route::post('/onboarding/tasks', [OnboardingController::class, 'storeTask'])->name('onboarding.tasks.store');
Route::put('/onboarding/tasks/{onboardingTask}', [OnboardingController::class, 'updateTask'])->name('onboarding.tasks.update');
Route::patch('/onboarding/tasks/{onboardingTask}/toggle', [OnboardingController::class, 'toggleTask'])->name('onboarding.tasks.toggle');
Route::post('/onboarding/assignments', [OnboardingController::class, 'assign'])->name('onboarding.assignments.store');
Route::patch('/onboarding/assignments/{assignment}/status', [OnboardingController::class, 'updateStatus'])->name('onboarding.assignments.status');
Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
Route::get('/training/{training}', [TrainingController::class, 'show'])->name('training.show');
Route::post('/training', [TrainingController::class, 'store'])->name('training.store');
Route::put('/training/{training}', [TrainingController::class, 'update'])->name('training.update');
Route::delete('/training/{training}', [TrainingController::class, 'destroy'])->name('training.destroy');
Route::get('/departemen', [DepartemenController::class, 'index'])->name('departemen.index');
Route::post('/departemen', [DepartemenController::class, 'store'])->name('departemen.store');
Route::put('/departemen/{departemen}', [DepartemenController::class, 'update'])->name('departemen.update');
Route::delete('/departemen/{departemen}', [DepartemenController::class, 'destroy'])->name('departemen.destroy');
Route::get('/asset', [AssetController::class, 'index'])->name('asset.index');
Route::post('/asset', [AssetController::class, 'store'])->name('asset.store');
Route::put('/asset/{asset}', [AssetController::class, 'update'])->name('asset.update');

Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
Route::get('/karyawan/{id}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');

Route::get('/staffmgr', [KaryawanstaffController::class, 'index'])->name('staff.index');
Route::get('/staff', [KaryawanstaffController::class, 'index'])->name('staff.all');
Route::post('/staffmgr', [KaryawanstaffController::class, 'store'])->name('staff.store');
Route::get('/staffmgr/{id}', [KaryawanstaffController::class, 'show'])->name('staff.show');
Route::post('/staffmgr/{karyawanstaff}/facilities', [KaryawanstaffController::class, 'storeFacility'])
    ->name('staff.facilities.store');
Route::put('/staffmgr/{karyawanstaff}/training-participants/{trainingParticipant}', [KaryawanstaffController::class, 'updateTrainingParticipant'])
    ->name('staff.training-participants.update');
Route::post('/staffmgr/{karyawanstaff}/onboarding', [OnboardingController::class, 'assignToStaff'])->name('onboarding.staff.assign');
Route::get('/staffmgr/{id}/edit', [KaryawanstaffController::class, 'edit'])->name('staff.edit');
Route::put('/staffmgr/{id}', [KaryawanstaffController::class, 'update'])->name('staff.update');


// route produksi
Route::get('/get-job-spreadsheet/{id}', [SpreadsheetController::class, 'getJob']);
Route::get('/spreadsheet', [SpreadsheetController::class, 'index'])->name('spreadsheet.index');
Route::post('/spreadsheet', [SpreadsheetController::class, 'store'])->name('spreadsheet.store');
Route::get('/proses-produksi', [SpreadsheetController::class, 'indexdata'])->name('proses-produksi.indexdata');
Route::get('/proses-produksi/search-suggestions', [SpreadsheetController::class, 'searchSuggestions'])->name('proses-produksi.search-suggestions');
Route::get('/proses-produksi/job/{job_id}', [SpreadsheetController::class, 'report'])->name('proses-produksi.rangkuman');
Route::match(['post', 'patch'], '/proses-produksi/inline-update', [SpreadsheetController::class, 'inlineUpdate'])->name('proses-produksi.inline-update');
Route::get('/get-job-data/{job_id}', [SpreadsheetController::class, 'getJobData']);
Route::put('/proses-produksi/{id}', [SpreadsheetController::class, 'update'])->name('proses-produksi.update');
Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
Route::get('/activity-logs/proses/{proses_produksi_id}', [ActivityLogController::class, 'showByProses'])->name('activity-logs.show-by-proses');
Route::get('/export/production-summary', [ExportController::class, 'exportProductionSummary'])->name('export.production-summary');
Route::get('/export/production-mutasi', [ExportController::class, 'exportProductionMutasi'])->name('export.production-mutasi');
Route::get('/export/production-mutasi-filter', [ExportController::class, 'exportProductionMutasiFiltered'])->name('export.production-mutasi-filter');
Route::get('/export/production-filter', [ExportController::class, 'exportProductionMutasiFiltered'])->name('export.production-filter');
Route::get('/export/production-array', [ExportController::class, 'exportProductionArray'])->name('export.production-array');
Route::post('/proses-produksi/bulk-destroy', [SpreadsheetController::class, 'bulkDestroy'])->name('proses-produksi.bulk-destroy');
Route::delete('/proses-produksi/{id}', [SpreadsheetController::class, 'destroy'])->name('proses-produksi.destroy');
