<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FacebookConfig extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'page_name',
        'page_id',
        'access_token',
        'is_active',
        'fetch_limit',
        'article_category',
        'article_author',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fetch_limit' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCollege($query, string $collegeSlug)
    {
        return $query->where(function ($q) use ($collegeSlug) {
            $q->where('entity_type', 'college')
              ->where('entity_id', $collegeSlug)
              ->orWhere('entity_type', 'global');
        });
    }

    public function scopeForDepartment($query, int $departmentId)
    {
        return $query->where(function ($q) use ($departmentId) {
            $q->where('entity_type', 'department')
              ->where('entity_id', $departmentId);
        });
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where(function ($q) use ($organizationId) {
            $q->where('entity_type', 'organization')
              ->where('entity_id', $organizationId);
        });
    }

    public function getRouteKey(): string
    {
        return $this->routeKeyBase();
    }

    public static function findByRouteKey(string|int $value): ?self
    {
        if (is_numeric($value)) {
            return static::find((int) $value);
        }

        $routeKey = trim((string) $value);

        return static::query()
            ->get()
            ->first(function (self $config) use ($routeKey) {
                return $config->routeKeyBase() === $routeKey;
            });
    }

    protected function routeKeyBase(): string
    {
        return match ($this->entity_type) {
            'college' => (string) $this->entity_id,
            'department', 'organization' => Str::slug($this->resolveEntityName() ?: $this->page_name ?: $this->entity_id),
            'global' => 'global',
            default => Str::slug($this->page_name ?: $this->entity_id ?: 'facebook-config'),
        };
    }

    protected function resolveEntityName(): ?string
    {
        return match ($this->entity_type) {
            'college' => \App\Models\College::query()
                ->where('slug', $this->entity_id)
                ->value('name'),
            'department' => \App\Models\CollegeDepartment::query()
                ->whereKey($this->entity_id)
                ->value('name'),
            'organization' => \App\Models\CollegeOrganization::query()
                ->whereKey($this->entity_id)
                ->value('name'),
            default => null,
        };
    }
}
