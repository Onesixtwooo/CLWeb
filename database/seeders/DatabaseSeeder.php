<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'adminCLSU@clsu.edu'],
            [
                'name' => 'CLSU admin',
                'password' => Hash::make('!CLSUCi$@_2026'),
                'is_admin' => true,
                'role' => User::ROLE_SUPERADMIN,
                'college_slug' => null,
            ]
        );
    }
}
