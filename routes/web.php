<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;

// ── Guest routes (redirect to dashboard if already logged in) ─────────────
Route::middleware('guest')->group(function () {
    Route::get('/',        [LoginController::class, 'showLogin'])->name('login');
    Route::post('/',       [LoginController::class, 'login']);        // ← added
    Route::post('/login',  [LoginController::class, 'login']);

    Route::get('/register',  [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// ── Authenticated routes ──────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users CRUD
    Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);

    // Meeting Notes CRUD + JSON endpoint for modal view
    Route::get('/notes/{note}/json', [NoteController::class, 'json'])->name('notes.json');
    Route::resource('notes', NoteController::class)->except(['show', 'create', 'edit']);

    // Profile
    Route::get('/profile',          [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar',  [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});