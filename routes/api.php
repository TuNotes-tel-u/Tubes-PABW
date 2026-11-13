<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReadingController;

Route::apiResource('readings', ReadingController::class)
    ->names('api.readings');

Route::apiResource('study-list', \App\Http\Controllers\Api\CourseController::class)
    ->parameters(['study-list' => 'course']);