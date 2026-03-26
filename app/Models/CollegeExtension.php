<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_slug',
        'title',
        'description',
        'image',
        'is_visible',
        'is_draft',
        'publish_at',
        'sort_order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_draft' => 'boolean',
        'publish_at' => 'datetime',
    ];
}
