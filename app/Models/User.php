<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'college_slug',
        'department',
        'organization_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isEditor(): bool
    {
        return $this->role === self::ROLE_EDITOR;
    }

    /** Whether this user can access the given college (by slug). Superadmin can access all; editor and admin only their assigned college. */
    public function canAccessCollege(?string $collegeSlug): bool
    {
        if ($collegeSlug === null) {
            return true;
        }
        if ($this->role === self::ROLE_SUPERADMIN) {
            return true;
        }
        // Editor and admin are only for their assigned college
        return $this->college_slug === $collegeSlug;
    }

    /** Whether this user can manage (create, edit, delete) content for the given college. Superadmin can manage all. College admins can only manage content explicitly bound to their college. */
    public function canManageCollegeContent(?string $collegeSlug): bool
    {
        if ($this->role === self::ROLE_SUPERADMIN) {
            return true;
        }

        // College admins cannot manage global content (slug === null)
        if ($collegeSlug === null) {
            return false;
        }

        return $this->college_slug === $collegeSlug;
    }

    /** Whether this user is restricted to a single college (editor and admin always are). */
    public function isBoundedToCollege(): bool
    {
        if ($this->role === self::ROLE_SUPERADMIN) {
            return false;
        }
        return true;
    }

    /**
     * Whether this user is restricted to a single department.
     * Returns true if user has both college_slug AND department assigned.
     */
    public function isBoundedToDepartment(): bool
    {
        if ($this->role === self::ROLE_SUPERADMIN) {
            return false;
        }
        return !empty($this->college_slug) && !empty($this->department);
    }

    /**
     * Whether this user is restricted to a single organization.
     */
    public function isBoundedToOrganization(): bool
    {
        if ($this->role === self::ROLE_SUPERADMIN) {
            return false;
        }
        return !empty($this->organization_id);
    }

    /**
     * Whether this user can access the given department.
     * Superadmin can access all departments.
     * College admin can access all departments in their college.
     * Department admin/editor can only access their assigned department.
     */
    public function canAccessDepartment(?string $collegeSlug, ?string $departmentName): bool
    {
        if ($departmentName === null) {
            return true;
        }

        if ($this->role === self::ROLE_SUPERADMIN) {
            return true;
        }

        // Must be in the correct college first
        if (!$this->canAccessCollege($collegeSlug)) {
            return false;
        }

        // If user is bounded to an organization, check access
        if ($this->isBoundedToOrganization()) {
            $org = $this->organization;
            if ($org) {
                if ($org->department_id) {
                    return $org->department?->name === $departmentName;
                }
                return false; // college-wide org shouldn't have access to specific department actions
            }
        }

        // If user is bounded to a department, check department match
        if ($this->isBoundedToDepartment()) {
            return $this->department === $departmentName;
        }

        // College admin (not bounded to department) can access all departments in their college
        return true;
    }

    /**
     * Whether this user can access the given organization.
     */
    public function canAccessOrganization(?int $organizationId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isBoundedToOrganization()) {
            return $this->organization_id === $organizationId;
        }

        if (!empty($organizationId)) {
            $org = CollegeOrganization::find($organizationId);
            if ($org) {
                return $this->canAccessCollege($org->college_slug) && $this->canAccessDepartment($org->college_slug, $org->department?->name);
            }
        }

        return true;
    }

    public function organization()
    {
        return $this->belongsTo(CollegeOrganization::class, 'organization_id');
    }

    /**
     * Get the department ID for this user's assigned department.
     * Returns null if user is not bounded to a department.
     */
    public function getDepartmentId(string $collegeSlug): ?int
    {
        if (!$this->isBoundedToDepartment()) {
            return null;
        }

        $dept = \App\Models\CollegeDepartment::where('college_slug', $collegeSlug)
            ->where('name', $this->department)
            ->first();

        return $dept ? $dept->id : null;
    }

    public function getDepartmentRouteKey(string $collegeSlug): ?string
    {
        if (!$this->isBoundedToDepartment()) {
            return null;
        }

        $dept = \App\Models\CollegeDepartment::where('college_slug', $collegeSlug)
            ->where('name', $this->department)
            ->first();

        return $dept?->getRouteKey();
    }

    /**
     * The default permission configuration for all role types.
     * A value of false = restriction enforced (cannot do it).
     * A value of true  = restriction lifted (can do it).
     */
    public static function defaultRolePermissions(): array
    {
        return [
            'college-admin' => [
                'can_access_other_colleges'    => false,
                'can_change_global_settings'   => false,
                'can_create_superadmin'        => false,
            ],
            'dept-admin' => [
                'can_access_other_departments' => false,
                'can_manage_college_settings'  => false,
                'can_manage_users_outside_dept'=> false,
            ],
            'org-admin' => [
                'can_access_outside_org'           => false,
                'can_manage_users_or_departments'  => false,
                'can_access_college_settings'      => false,
            ],
            'editor' => [
                'can_manage_users'              => false,
                'can_change_settings'           => false,
                'unrestricted_scope'            => false,
            ],
        ];
    }

    /**
     * Load persisted role permissions from settings, merged with defaults.
     */
    public static function getRolePermissions(): array
    {
        $defaults = self::defaultRolePermissions();
        $stored   = \App\Models\Setting::get('role_permissions');

        if (!$stored) {
            return $defaults;
        }

        $decoded = is_string($stored) ? json_decode($stored, true) : $stored;

        if (!is_array($decoded)) {
            return $defaults;
        }

        // Deep merge: stored overrides defaults
        foreach ($defaults as $role => $perms) {
            if (isset($decoded[$role]) && is_array($decoded[$role])) {
                $defaults[$role] = array_merge($perms, $decoded[$role]);
            }
        }

        return $defaults;
    }

    /**
     * Determine what "role type" string this user maps to for permissions.
     */
    public function getRoleType(): string
    {
        if ($this->role === self::ROLE_EDITOR) return 'editor';
        if ($this->role === self::ROLE_SUPERADMIN) return 'superadmin';
        if (!empty($this->organization_id)) return 'org-admin';
        if (!empty($this->department)) return 'dept-admin';
        return 'college-admin';
    }

    /**
     * Check if this user has a specific permission lifted (i.e., the restriction was removed).
     * Returns true if the restriction is lifted (user CAN do it).
     */
    public function canDo(string $permissionKey): bool
    {
        if ($this->isSuperAdmin()) return true;

        $roleType = $this->getRoleType();
        $permissions = self::getRolePermissions();

        return (bool) ($permissions[$roleType][$permissionKey] ?? false);
    }

    public function canManageArticle(Article $article): bool
    {
        if (! $this->canManageCollegeContent($article->college_slug)) {
            return false;
        }

        if (! $this->isBoundedToDepartment()) {
            return true;
        }

        $articleOwner = $article->user;

        if (! $articleOwner || ! $articleOwner->isBoundedToDepartment()) {
            return false;
        }

        return $articleOwner->college_slug === $this->college_slug
            && $articleOwner->department === $this->department;
    }

    public function canManageAnnouncement(Announcement $announcement): bool
    {
        if (! $this->canManageCollegeContent($announcement->college_slug)) {
            return false;
        }

        if (! $this->isBoundedToDepartment()) {
            return true;
        }

        $announcementOwner = $announcement->user;

        if (! $announcementOwner || ! $announcementOwner->isBoundedToDepartment()) {
            return false;
        }

        return $announcementOwner->college_slug === $this->college_slug
            && $announcementOwner->department === $this->department;
    }
}
