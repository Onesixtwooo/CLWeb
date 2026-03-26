<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create-user {email=adminCLSU@clsu.edu : Admin email} {password=!CLSUCi$@_2026 : Admin password}', function () {
    $email = $this->argument('email');
    $password = $this->argument('password');

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => 'CLSU admin',
            'password' => Hash::make($password),
            'is_admin' => true,
            'role' => User::ROLE_SUPERADMIN,
            'college_slug' => null,
        ]
    );

    $this->info('Admin user ready.');
    $this->line('Email: ' . $user->email);
    $this->line('Password: (the one you provided)');
    $this->newLine();
    $this->comment('Login at: ' . url('/admin/login'));
})->purpose('Create or reset the admin user for the CMS');
