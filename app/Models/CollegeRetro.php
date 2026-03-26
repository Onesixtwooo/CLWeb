<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeRetro extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_slug',
        'department_id',
        'title',
        'description',
        'stamp',
        'background_image',
        'sort_order',
        'is_visible',
        'title_size',
        'stamp_size',
    ];

    public function college()
    {
        return $this->belongsTo(College::class, 'college_slug', 'slug');
    }

    public function department()
    {
        return $this->belongsTo(CollegeDepartment::class, 'department_id');
    }
}
