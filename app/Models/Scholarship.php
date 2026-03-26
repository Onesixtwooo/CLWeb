<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_slug',
        'title',
        'description',
        'qualifications',
        'requirements',
        'process',
        'benefits',
        'image',
        'added_by',
        'user_id',
        'sort_order',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this scholarship is locked for the given user.
     * Superadmin-added scholarships are locked for regular admins.
     */
    public function isLockedFor(?User $user): bool
    {
        if (!$user) return true;
        if ($user->isSuperAdmin()) return false;
        return $this->added_by === 'superadmin';
    }
}
