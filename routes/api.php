<?php

use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\ReadingController;
use Illuminate\Support\Facades\Route;

// Notes (TuNotes, modul Reeyhan)
// name: notes.index, notes.store, notes.show, notes.update, notes.destroy
Route::apiResource('notes', NoteController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);

// Readings (modul Afifa)
Route::apiResource('readings', ReadingController::class)
    ->names('api.readings');

// Study List / mata kuliah (modul Naufal)
Route::apiResource('study-list', \App\Http\Controllers\Api\CourseController::class)
    ->parameters(['study-list' => 'course']);
