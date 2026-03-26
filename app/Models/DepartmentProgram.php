<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentProgram extends Model
{
    protected $fillable = [
        'department_id',
        'institute_id',
        'title',
        'numbering',
        'description',
        'image',
        'sort_order',
        'numbered_content',
    ];

    protected $casts = [
        'numbered_content' => 'array',
    ];

    /**
     * Get the department that owns the program.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(CollegeDepartment::class, 'department_id');
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(CollegeInstitute::class, 'institute_id');
    }
}
