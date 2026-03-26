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
        Schema::table('college_contacts', function (Blueprint $table) {
            $table->dropColumn(['twitter', 'youtube', 'linkedin', 'website']);
            $table->json('custom_links')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_contacts', function (Blueprint $table) {
            $table->string('twitter')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('website')->nullable();
            $table->dropColumn('custom_links');
        });
    }
};
