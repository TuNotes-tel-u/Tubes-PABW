<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Mata kuliah (tabel courses). Dipakai notes (course_id) dan Study List.
class Course extends Model
{
    protected $fillable = ['semester_id', 'code', 'name', 'lecturer_name', 'sks'];

    protected $casts = [
        'sks' => 'integer',
    ];
}
