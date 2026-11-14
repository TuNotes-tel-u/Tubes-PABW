<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotePageController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReadingViewController;
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

    // Halaman Study List (modul Naufal), datanya diambil dari /api/study-list
    Route::view('/study-list', 'study-list.index')->name('study-list.page');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Route untuk Reading View (modul Afifa)
Route::get('/readings', [ReadingViewController::class, 'index'])->name('readings.index');
Route::get('/readings/create', [ReadingViewController::class, 'create'])->name('readings.create');
Route::post('/readings', [ReadingViewController::class, 'store'])->name('readings.store');
Route::get('/readings/{id}/edit', [ReadingViewController::class, 'edit'])->name('readings.edit');
Route::put('/readings/{id}', [ReadingViewController::class, 'update'])->name('readings.update');
Route::delete('/readings/{id}', [ReadingViewController::class, 'destroy'])->name('readings.destroy');
