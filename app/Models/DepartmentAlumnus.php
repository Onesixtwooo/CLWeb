<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DepartmentAlumnus extends Model
{
    protected $table = 'department_alumni';

    protected $fillable = [
        'college_slug',
        'department_id',
        'institute_id',
        'title',
        'description',
        'image',
        'year_graduated',
        'sort_order',
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
            ->first(function (self $alumnus) use ($routeKey) {
                return $alumnus->title === $routeKey || Str::slug($alumnus->title) === $routeKey;
            });
    }

    public static function findByCollegeAndRouteKey(string $collegeSlug, string|int $value): ?self
    {
        $query = static::where('college_slug', $collegeSlug)
            ->whereNull('department_id')
            ->whereNull('institute_id');

        if (is_numeric($value)) {
            return (clone $query)->find((int) $value);
        }

        $routeKey = trim((string) $value);

        return (clone $query)
            ->get()
            ->first(function (self $alumnus) use ($routeKey) {
                return $alumnus->title === $routeKey || Str::slug($alumnus->title) === $routeKey;
            });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(CollegeDepartment::class, 'department_id');
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(CollegeInstitute::class, 'institute_id');
    }
}
