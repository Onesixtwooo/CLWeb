<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DepartmentCurriculum extends Model
{
    protected $table = 'department_curricula';

    protected $fillable = [
        'department_id',
        'institute_id',
        'title',
        'courses',
        'sort_order',
    ];

    protected $casts = [
        // 'courses' => 'array',
    ];

    public function getRouteKey(): string
    {
        return Str::slug($this->title);
    }

    public static function findByDepartmentAndRouteKey(int $departmentId, string|int $value): ?self
    {
        $query = static::where('department_id', $departmentId);

        if (is_numeric($value)) {
            return (clone $query)->find((int) $value);
        }

        $routeKey = trim((string) $value);

        return (clone $query)
            ->get()
            ->first(function (self $curriculum) use ($routeKey) {
                return $curriculum->title === $routeKey || Str::slug($curriculum->title) === $routeKey;
            });
    }

    /**
     * Get the department that owns the curriculum.
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
