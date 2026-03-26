<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CollegeDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_slug',
        'title',
        'description',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'sort_order',
        'is_visible',
        'is_draft',
        'publish_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_visible' => 'boolean',
        'is_draft' => 'boolean',
        'publish_at' => 'datetime',
    ];

    public function getRouteKey(): string
    {
        return $this->id . '-' . Str::slug($this->title);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (preg_match('/^(\d+)(?:[-_].*)?$/', (string) $value, $matches)) {
            return $this->where('id', $matches[1])->first();
        }

        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    public function scopePublished($query)
    {
        return $query->where('is_draft', false)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            });
    }
}
