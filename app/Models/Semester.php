<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Semester (tabel semesters). Satu semester punya banyak mata kuliah.
class Semester extends Model
{
    protected $fillable = ['nama'];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
