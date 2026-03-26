<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeSection extends Model
{
    protected $fillable = [
        'college_slug',
        'section_slug',
        'title',
        'body',
        'is_visible',
        'is_draft',
        'publish_at',
        'meta',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_draft' => 'boolean',
        'publish_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Scope: only published sections (not draft, and publish_at has passed or is null).
     */
    public function scopePublished($query)
    {
        return $query->where('is_draft', false)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                  ->orWhere('publish_at', '<=', now());
            });
    }

    /**
     * Check if this section is currently published (not draft and publish_at has passed or is null).
     */
    public function isPublished(): bool
    {
        if ($this->is_draft) {
            return false;
        }
        if ($this->publish_at && $this->publish_at->isFuture()) {
            return false;
        }
        return true;
    }

    /**
     * Get the publish status label.
     */
    public function getStatusLabel(): string
    {
        if ($this->is_draft) {
            return 'Draft';
        }
        if ($this->publish_at && $this->publish_at->isFuture()) {
            return 'Scheduled';
        }
        return 'Published';
    }

    /**
     * Get the status color for badges.
     */
    public function getStatusColor(): string
    {
        return match ($this->getStatusLabel()) {
            'Draft' => '#f59e0b',
            'Scheduled' => '#8b5cf6',
            default => '#22c55e',
        };
    }
}
