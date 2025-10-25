<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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
