<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    // Mata kuliah di dalam Study List (satu baris = satu mata kuliah di satu semester)
    protected $fillable = ['semester_id', 'kode_mk', 'nama_mk', 'sks', 'dosen'];

    protected $casts = [
        'sks' => 'integer',
    ];
}
