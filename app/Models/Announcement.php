<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'body',
        'author',
        'image',
        'images',
        'banner_dark',
        'published_at',
        'college_slug',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'images' => 'array',
            'banner_dark' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
