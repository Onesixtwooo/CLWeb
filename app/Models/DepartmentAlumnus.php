<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
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

    protected static ?bool $hasCollegeSlugColumn = null;

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
        if (! static::supportsDirectCollegeAssignments()) {
            return null;
        }

        $query = static::query()
            ->where('college_slug', $collegeSlug)
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

    public function scopeVisibleForCollege(Builder $query, string $collegeSlug, bool $respectDepartmentVisibility = true): Builder
    {
        return $query->where(function (Builder $alumniQuery) use ($collegeSlug, $respectDepartmentVisibility) {
            if (static::supportsDirectCollegeAssignments()) {
                $alumniQuery->where(function (Builder $directQuery) use ($collegeSlug) {
                    $directQuery->where('college_slug', $collegeSlug)
                        ->whereNull('department_id')
                        ->whereNull('institute_id');
                });
            }

            $alumniQuery->orWhereHas('department', function (Builder $departmentQuery) use ($collegeSlug, $respectDepartmentVisibility) {
                $departmentQuery->where('college_slug', $collegeSlug);

                if ($respectDepartmentVisibility) {
                    $departmentQuery->where('alumni_is_visible', true);
                }
            });
        });
    }

    public static function supportsDirectCollegeAssignments(): bool
    {
        if (static::$hasCollegeSlugColumn === null) {
            static::$hasCollegeSlugColumn = Schema::hasColumn('department_alumni', 'college_slug');
        }

        return static::$hasCollegeSlugColumn;
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
