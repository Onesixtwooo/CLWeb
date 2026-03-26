<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    protected $table = 'facilities';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'photo',
        'sort_order',
        'college_slug',
        'department_name',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($facility) {
            if (empty($facility->slug)) {
                $facility->slug = \Illuminate\Support\Str::slug($facility->name);
                
                // Ensure uniqueness
                $originalSlug = $facility->slug;
                $count = 1;
                while (static::where('slug', $facility->slug)->where('id', '!=', $facility->id)->exists()) {
                    $facility->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FacilityImage::class)->orderBy('sort_order')->orderBy('created_at');
    }
}
