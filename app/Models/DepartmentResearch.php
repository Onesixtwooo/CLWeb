<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentResearch extends Model
{
    protected $table = 'department_research';

    protected $fillable = [
        'department_id',
        'institute_id',
        'title',
        'description',
        'completed_year',
        'image',
        'sort_order',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(CollegeDepartment::class, 'department_id');
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(CollegeInstitute::class, 'institute_id');
    }
}
