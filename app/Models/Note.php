<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    /**
     * Kategori bawaan platform TuNotes. Selalu tersedia pada dropdown
     * category tanpa harus dibuat oleh pengguna.
     */
    public const DEFAULT_CATEGORIES = [
        'TuNotes',
        'Kuliah',
        'Tugas',
        'Ringkasan',
        'Lainnya',
    ];

    public const FILE_TYPES = ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx'];

    protected $table = 'notes';

    protected $fillable = [
        'user_id',
        'course_id',
        'title',
        'course_name',
        'lecturer_name',
        'category',
        'tags',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'page_count',
        'is_public',
        'downloads_count',
        'view_count',
        'avg_rating',
        'rating_count',
        'status',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'file_size' => 'integer',
        'page_count' => 'integer',
        'downloads_count' => 'integer',
        'view_count' => 'integer',
        'avg_rating' => 'decimal:1',
        'rating_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * display_title selalu ikut pada response JSON maupun view.
     * Jika title kosong, gunakan file_name sebagai fallback.
     */
    protected $appends = ['display_title'];

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: $this->file_name;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
