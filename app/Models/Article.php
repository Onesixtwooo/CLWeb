<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'body',
        'banner',
        'banner_dark',
        'category',
        'author',
        'published_at',
        'user_id',
        'college_slug',
        'images',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'banner_dark' => 'boolean',
            'images' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDateFormattedAttribute(): string
    {
        return $this->published_at?->format('F j, Y') ?? $this->created_at->format('F j, Y');
    }
}
