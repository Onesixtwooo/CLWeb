<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeContact extends Model
{
    protected $fillable = [
        'college_slug',
        'email',
        'phone',
        'address',
        'facebook',
        'twitter',
        'instagram',
        'youtube',
        'linkedin',
        'website',
        'custom_links',
    ];

    protected $casts = [
        'custom_links' => 'array',
    ];
}
