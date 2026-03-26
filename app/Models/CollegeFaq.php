<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeFaq extends Model
{
    protected $fillable = [
        'college_slug',
        'question',
        'answer',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];
}
