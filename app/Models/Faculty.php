<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Faculty extends Model
{
    protected $table = 'faculty';

    protected $fillable = [
        'name',
        'position',
        'department',
        'email',
        'photo',
        'sort_order',
        'college_slug',
        'user_id',
        'institute_id',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKey(): string
    {
        return Str::slug($this->name);
    }

    public static function findByRouteKey(string|int $value): ?self
    {
        if (is_numeric($value)) {
            return static::find((int) $value);
        }

        $routeKey = trim((string) $value);

        return static::query()
            ->get()
            ->first(function (self $faculty) use ($routeKey) {
                return $faculty->name === $routeKey || Str::slug($faculty->name) === $routeKey;
            });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(CollegeInstitute::class, 'institute_id');
    }
}
