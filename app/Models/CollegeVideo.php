<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeVideo extends Model
{
    protected $fillable = [
        'college_slug',
        'video_type',
        'video_url',
        'video_file',
        'video_title',
        'video_description',
        'is_visible',
    ];

    /**
     * Get the college that owns the video.
     */
    public function college()
    {
        return $this->belongsTo(College::class, 'college_slug', 'slug');
    }
}
