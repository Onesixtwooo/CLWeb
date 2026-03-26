<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeOrganization extends Model
{
    protected $fillable = [
        'college_slug',
        'department_id',
        'name',
        'acronym',
        'description',
        'logo',
        'adviser',
        'is_visible',
        'sort_order',
        'sections',
    ];

    public function getRouteKeyName(): string
    {
        return 'acronym';
    }

    /**
     * Use acronym when available, otherwise fall back to numeric id.
     */
    public function getRouteKey(): mixed
    {
        return $this->acronym ?: $this->getKey();
    }

    /**
     * Support binding by acronym or by id.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: $this->getRouteKeyName();

        $query = $this->newQuery();

        // First try binding by acronym (default), then fall back to id.
        $model = $query->where($field, $value)->first();
        if (! $model && $field === $this->getRouteKeyName() && is_numeric($value)) {
            $model = $query->where($this->getKeyName(), $value)->first();
        }

        return $model;
    }

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
        'sections' => 'array',
    ];

    public function getSection(string $sectionSlug): ?array
    {
        $stored = $this->sections ?? [];
        return $stored[$sectionSlug] ?? null;
    }

    public function setSection(string $sectionSlug, array $content): void
    {
        $stored = $this->sections ?? [];
        $stored[$sectionSlug] = $content;
        $this->sections = $stored;
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_slug', 'slug');
    }

    public function department()
    {
        return $this->belongsTo(CollegeDepartment::class, 'department_id');
    }
}
