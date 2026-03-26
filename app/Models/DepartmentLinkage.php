<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function department()
    {
        return $this->belongsTo(CollegeDepartment::class, 'department_id');
    }
}
