<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserPermissionsTest extends TestCase
{
    public function test_superadmin_has_global_college_access(): void
    {
        $user = new User([
            'role' => User::ROLE_SUPERADMIN,
        ]);

        $this->assertTrue($user->canAccessCollege('engineering'));
        $this->assertTrue($user->canManageCollegeContent('engineering'));
        $this->assertTrue($user->canManageCollegeContent(null));
    }

    public function test_college_admin_is_limited_to_assigned_college(): void
    {
        $user = new User([
            'role' => User::ROLE_ADMIN,
            'college_slug' => 'engineering',
        ]);

        $this->assertTrue($user->canAccessCollege('engineering'));
        $this->assertFalse($user->canAccessCollege('agriculture'));
        $this->assertTrue($user->canManageCollegeContent('engineering'));
        $this->assertFalse($user->canManageCollegeContent(null));
    }

    public function test_department_bounded_user_can_only_access_own_department(): void
    {
        $user = new User([
            'role' => User::ROLE_EDITOR,
            'college_slug' => 'engineering',
            'department' => 'Information Technology',
        ]);

        $this->assertTrue($user->canAccessDepartment('engineering', 'Information Technology'));
        $this->assertFalse($user->canAccessDepartment('engineering', 'Civil Engineering'));
        $this->assertFalse($user->canAccessDepartment('agriculture', 'Information Technology'));
    }

    public function test_editor_is_bounded_to_college_scope(): void
    {
        $user = new User([
            'role' => User::ROLE_EDITOR,
            'college_slug' => 'engineering',
        ]);

        $this->assertTrue($user->isBoundedToCollege());
        $this->assertFalse($user->isBoundedToDepartment());
        $this->assertFalse($user->isBoundedToOrganization());
    }
}
