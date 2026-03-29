<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
        'department_name',
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

    public function getRouteKey(): string
    {
        return $this->slug ?: (string) $this->id;
    }

    public static function findByRouteKey(string|int $value): ?self
    {
        if (is_numeric($value)) {
            return static::find((int) $value);
        }

        $routeKey = trim((string) $value);

        return static::query()
            ->where('slug', $routeKey)
            ->first();
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug !== '' ? $baseSlug : 'announcement';
        $suffix = 2;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'announcement') . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
