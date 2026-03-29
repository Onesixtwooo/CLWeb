<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DepartmentLinkage extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'description',
        'image',
        'url',
        'type',
        'sort_order',
    ];

    public function getRouteKey(): string
    {
        return Str::slug($this->name);
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
            ->first(function (self $linkage) use ($routeKey) {
                return $linkage->name === $routeKey || Str::slug($linkage->name) === $routeKey;
            });
    }

    public function department()
    {
        return $this->belongsTo(CollegeDepartment::class, 'department_id');
    }
}
