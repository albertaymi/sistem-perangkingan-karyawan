<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\DropdownOptionController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PerhitunganController;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// Authentication Routes (Guest only)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout (Authenticated only)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (Protected - semua role yang login & approved)
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard')->middleware('auth');

// User Management Routes (Super Admin & HRD only)
Route::middleware(['auth', 'role:super_admin,hrd'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('update');
    Route::post('/{id}/approve', [UserController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [UserController::class, 'reject'])->name('reject');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
});

// Kriteria Management Routes (Super Admin & HRD only)
Route::middleware(['auth', 'role:super_admin,hrd'])->prefix('kriteria')->name('kriteria.')->group(function () {
    Route::get('/', [KriteriaController::class, 'index'])->name('index');
    Route::post('/', [KriteriaController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [KriteriaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [KriteriaController::class, 'update'])->name('update');
    Route::delete('/{id}', [KriteriaController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [KriteriaController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/total-bobot', [KriteriaController::class, 'getTotalBobot'])->name('total-bobot');

    // Detail Kriteria & Sub-Kriteria Management
    Route::get('/{kriteriaId}/detail', [KriteriaController::class, 'detail'])->name('detail');
    Route::post('/{kriteriaId}/sub-kriteria', [SubKriteriaController::class, 'store'])->name('sub-kriteria.store');
    Route::get('/{kriteriaId}/sub-kriteria/{id}/edit', [SubKriteriaController::class, 'edit'])->name('sub-kriteria.edit');
    Route::put('/{kriteriaId}/sub-kriteria/{id}', [SubKriteriaController::class, 'update'])->name('sub-kriteria.update');
    Route::delete('/{kriteriaId}/sub-kriteria/{id}', [SubKriteriaController::class, 'destroy'])->name('sub-kriteria.destroy');
    Route::post('/{kriteriaId}/sub-kriteria/{id}/toggle-status', [SubKriteriaController::class, 'toggleStatus'])->name('sub-kriteria.toggle-status');
    Route::get('/{kriteriaId}/sub-kriteria/total-bobot', [SubKriteriaController::class, 'getTotalBobot'])->name('sub-kriteria.total-bobot');

    // Dropdown Options Management (Level 3)
    Route::get('/{kriteriaId}/sub-kriteria/{subKriteriaId}/options', [DropdownOptionController::class, 'index'])->name('dropdown-options.index');
    Route::post('/{kriteriaId}/sub-kriteria/{subKriteriaId}/options', [DropdownOptionController::class, 'store'])->name('dropdown-options.store');
    Route::get('/{kriteriaId}/sub-kriteria/{subKriteriaId}/options/{id}/edit', [DropdownOptionController::class, 'edit'])->name('dropdown-options.edit');
    Route::put('/{kriteriaId}/sub-kriteria/{subKriteriaId}/options/{id}', [DropdownOptionController::class, 'update'])->name('dropdown-options.update');
    Route::delete('/{kriteriaId}/sub-kriteria/{subKriteriaId}/options/{id}', [DropdownOptionController::class, 'destroy'])->name('dropdown-options.destroy');
    Route::post('/{kriteriaId}/sub-kriteria/{subKriteriaId}/options/{id}/toggle-status', [DropdownOptionController::class, 'toggleStatus'])->name('dropdown-options.toggle-status');
    Route::post('/{kriteriaId}/sub-kriteria/{subKriteriaId}/options/update-urutan', [DropdownOptionController::class, 'updateUrutan'])->name('dropdown-options.update-urutan');
});

// Penilaian Karyawan Routes (Super Admin, HRD & Supervisor)
Route::middleware(['auth', 'role:super_admin,hrd,supervisor'])->prefix('penilaian')->name('penilaian.')->group(function () {
    Route::get('/', [PenilaianController::class, 'index'])->name('index');
    Route::get('/{karyawanId}/{bulan}/{tahun}/overview', [PenilaianController::class, 'overview'])->name('overview');
    Route::get('/create', [PenilaianController::class, 'create'])->name('create');
    Route::post('/', [PenilaianController::class, 'store'])->name('store');
    Route::get('/{karyawanId}/{bulan}/{tahun}', [PenilaianController::class, 'show'])->name('show');
    Route::get('/{karyawanId}/{bulan}/{tahun}/edit', [PenilaianController::class, 'edit'])->name('edit');
    Route::put('/{karyawanId}/{bulan}/{tahun}', [PenilaianController::class, 'update'])->name('update');
    Route::delete('/{karyawanId}/{bulan}/{tahun}', [PenilaianController::class, 'destroy'])->name('destroy');
});

// Perhitungan & Ranking Routes (Super Admin & HRD only)
Route::middleware(['auth', 'role:super_admin,hrd'])->prefix('perhitungan')->name('perhitungan.')->group(function () {
    Route::get('/', [PerhitunganController::class, 'index'])->name('index');
    Route::post('/calculate', [PerhitunganController::class, 'calculate'])->name('calculate');
    Route::get('/detail/{id}', [PerhitunganController::class, 'detail'])->name('detail');
    Route::get('/{bulan}/{tahun}', [PerhitunganController::class, 'show'])->name('show');
});

// Hasil Perangkingan Routes (Semua role yang login & approved dapat melihat)
Route::middleware(['auth'])->prefix('ranking')->name('ranking.')->group(function () {
    Route::get('/', [PerhitunganController::class, 'rankingIndex'])->name('index');
    Route::get('/detail/{id}', [PerhitunganController::class, 'detail'])->name('detail');
    Route::get('/{bulan}/{tahun}', [PerhitunganController::class, 'show'])->name('show');

    // Export routes
    Route::get('/export-pdf/{bulan}/{tahun}', [PerhitunganController::class, 'exportPDF'])->name('export.pdf');
    Route::get('/export-excel/{bulan}/{tahun}', [PerhitunganController::class, 'exportExcel'])->name('export.excel');
});

// Profile Routes (Semua role yang login dapat mengakses)
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::get('/change-password', [App\Http\Controllers\ProfileController::class, 'editPassword'])->name('edit-password');
    Route::put('/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('update-password');
});
