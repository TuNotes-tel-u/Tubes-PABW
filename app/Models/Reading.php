<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    protected $fillable = [
        'note_title',
        'status',
        'progress',
        'target_date',
    ];
}
