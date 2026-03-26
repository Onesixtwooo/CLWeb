<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $existingUserId = DB::table('users')
            ->where('email', 'adminCLSU@clsu.edu')
            ->value('id');

        DB::table('users')->updateOrInsert(
            ['email' => 'adminCLSU@clsu.edu'],
            [
                'name' => 'CLSU admin',
                'is_admin' => true,
                'role' => User::ROLE_SUPERADMIN,
                'college_slug' => null,
                'password' => Hash::make('!CLSUCi$@_2026'),
                'email_verified_at' => $now,
                'updated_at' => $now,
                'created_at' => $existingUserId ? DB::raw('created_at') : $now,
            ]
        );
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'adminCLSU@clsu.edu')
            ->delete();
    }
};
