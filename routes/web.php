<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReadingViewController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk Reading View
Route::get('/readings', [ReadingViewController::class, 'index'])->name('readings.index');
Route::get('/readings/create', [ReadingViewController::class, 'create'])->name('readings.create');
Route::post('/readings', [ReadingViewController::class, 'store'])->name('readings.store');
Route::get('/readings/{id}/edit', [ReadingViewController::class, 'edit'])->name('readings.edit');
Route::put('/readings/{id}', [ReadingViewController::class, 'update'])->name('readings.update');
Route::delete('/readings/{id}', [ReadingViewController::class, 'destroy'])->name('readings.destroy');
