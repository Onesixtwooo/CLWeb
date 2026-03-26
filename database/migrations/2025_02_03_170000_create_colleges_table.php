<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_COLLEGES = [
        'agriculture' => 'College of Agriculture',
        'arts-and-social-sciences' => 'College of Arts and Social Sciences',
        'business-and-accountancy' => 'College of Business and Accountancy',
        'education' => 'College of Education',
        'engineering' => 'College of Engineering',
        'fisheries' => 'College of Fisheries',
        'home-science-and-industry' => 'College of Home Science and Industry',
        'veterinary-science-and-medicine' => 'College of Veterinary Science and Medicine',
        'science' => 'College of Science',
    ];

    public function up(): void
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->string('slug', 80)->primary();
            $table->string('name');
            $table->timestamps();
        });

        foreach (self::DEFAULT_COLLEGES as $slug => $name) {
            DB::table('colleges')->insert([
                'slug' => $slug,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
