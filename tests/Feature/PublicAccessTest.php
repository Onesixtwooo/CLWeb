<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicAccessTest extends TestCase
{
    public static function publicPageProvider(): array
    {
        return [
            'homepage' => ['/'],
            'about page' => ['/about'],
            'privacy policy' => ['/privacy-policy'],
            'admin login page' => ['/admin/login'],
        ];
    }

    #[DataProvider('publicPageProvider')]
    public function test_public_and_entry_pages_load_successfully(string $uri): void
    {
        $this->get($uri)->assertOk();
    }

    public function test_admin_dashboard_redirects_guests_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect(route('admin.login'));
    }
}
