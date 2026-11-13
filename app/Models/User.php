<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password_hash',
        'role',
        'status',
        'avatar_url',
        'notes_count',
        'last_active_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Memetakan kolom autentikasi password ke password_hash
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}