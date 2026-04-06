<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin.testing@clsu.edu',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'is_admin' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_editor_role_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_EDITOR,
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin.invalid@clsu.edu',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'is_admin' => true,
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_non_admin_user_is_redirected_out_of_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'guest',
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect(route('admin.login'));
    }
}
