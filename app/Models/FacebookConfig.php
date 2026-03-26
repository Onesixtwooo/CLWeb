<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
