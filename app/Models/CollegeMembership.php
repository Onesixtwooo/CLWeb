<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollegeMembership extends Model
{
    protected $fillable = [
        'college_slug',
        'department_id',
        'organization',
        'membership_type',
        'logo',
        'description',
        'valid_until',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
        'valid_until' => 'date',
    ];

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class, 'college_slug', 'slug');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(CollegeDepartment::class, 'department_id');
    }
}
