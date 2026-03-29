<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DepartmentFacility extends Model
{
    protected $fillable = [
        'department_id',
        'institute_id',
        'title',
        'description',
        'image',
        'sort_order',
    ];

    public function getRouteKey(): string
    {
        $base = Str::slug($this->title);

        if ($base === '') {
            return (string) $this->id;
        }

        $duplicateCount = static::where('department_id', $this->department_id)
            ->where('title', $this->title)
            ->count();

        return $duplicateCount > 1 ? "{$base}-{$this->id}" : $base;
    }

    public static function findByDepartmentAndRouteKey(int $departmentId, string|int $value): ?self
    {
        $query = static::where('department_id', $departmentId);

        if (is_numeric($value)) {
            return (clone $query)->find((int) $value);
        }

        $routeKey = trim((string) $value);

        if (preg_match('/^(.*)-(\d+)$/', $routeKey, $matches)) {
            $candidate = (clone $query)->find((int) $matches[2]);
            if ($candidate && Str::slug($candidate->title) === $matches[1]) {
                return $candidate;
            }
        }

        return (clone $query)
            ->get()
            ->first(function (self $facility) use ($routeKey) {
                return $facility->title === $routeKey || Str::slug($facility->title) === $routeKey;
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
