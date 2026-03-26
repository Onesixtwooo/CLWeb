<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CollegeAccreditation extends Model
{
    protected $fillable = [
        'college_slug',
        'program_id',
        'agency',
        'logo',
        'level',
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(DepartmentProgram::class, 'program_id');
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class, 'college_slug', 'slug');
    }

    public function getAgencyAcronymAttribute(): string
    {
        $agency = trim((string) $this->agency);

        if ($agency === '') {
            return '';
        }

        preg_match_all('/\b([A-Z])/', $agency, $matches);
        $acronym = implode('', $matches[1] ?? []);

        if (Str::length($acronym) >= 2) {
            return $acronym;
        }

        return $agency;
    }
}
