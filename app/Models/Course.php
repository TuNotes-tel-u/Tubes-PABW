<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Mata kuliah (tabel courses). Dipakai notes (course_id) dan Study List.
class Course extends Model
{
    protected $fillable = ['semester_id', 'code', 'name', 'lecturer_name', 'sks'];

    protected $casts = [
        'sks' => 'integer',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    // Catatan yang terhubung ke mata kuliah ini (notes.course_id)
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
