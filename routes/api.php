<?php

use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Notes (TuNotes)
|--------------------------------------------------------------------------
| Satu-satunya sumber pendaftaran route notes. Tidak ada duplikasi antara
| route manual dan apiResource.
|
| name: notes.index   GET    /api/notes
| name: notes.store   POST   /api/notes
| name: notes.show    GET    /api/notes/{note}
| name: notes.update  PUT/PATCH /api/notes/{note}
| name: notes.destroy DELETE /api/notes/{note}
*/

Route::apiResource('notes', NoteController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);
