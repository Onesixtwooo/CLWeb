<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeTestimonial extends Model
{
    protected $fillable = [
        'college_slug',
        'name',
        'role',
        'degree',
        'quote',
        'photo',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];
}
