<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReadingController;

Route::apiResource('readings', ReadingController::class)
    ->names('api.readings');
