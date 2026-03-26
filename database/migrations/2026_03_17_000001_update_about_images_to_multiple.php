<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            // Drop the old single image column if it exists
            if (Schema::hasColumn('colleges', 'about_image')) {
                $table->dropColumn('about_image');
            }
            // Add the new multiple images column
            if (!Schema::hasColumn('colleges', 'about_images')) {
                $table->json('about_images')->nullable()->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropColumn('about_images');
        });
    }
};
