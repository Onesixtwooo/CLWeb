<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CollegeTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_slug',
        'title',
        'description',
        'image',
        'is_visible',
        'is_draft',
        'publish_at',
        'sort_order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_draft' => 'boolean',
        'publish_at' => 'datetime',
    ];

    /**
     * Use a more descriptive key for URLs (eg. "3-introduction-to-laravel").
     */
    public function getRouteKey(): string
    {
        return $this->id . '-' . Str::slug($this->title);
    }

    /**
     * Allow binding the model using either the numeric ID or the "id-slug" value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (preg_match('/^(\d+)(?:[-_].*)?$/', (string) $value, $matches)) {
            return $this->where('id', $matches[1])->first();
        }

        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }
}
