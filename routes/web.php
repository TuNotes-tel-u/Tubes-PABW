<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotePageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman utama diarahkan sesuai status login
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Route untuk Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route terproteksi (Hanya User yang Sudah Login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [NotePageController::class, 'dashboard'])->name('dashboard');

    // Halaman My Notes & kategori
    Route::get('/notes', [NotePageController::class, 'index'])->name('notes.page');
    Route::get('/notes/upload', [NotePageController::class, 'create'])->name('notes.upload');
    Route::get('/notes/category/{category}', [NotePageController::class, 'category'])->name('notes.category');
    Route::get('/notes/{note}/edit', [NotePageController::class, 'edit'])->name('notes.edit');
    Route::post('/notes/categories', [NotePageController::class, 'storeCategory'])->name('notes.categories.store');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
