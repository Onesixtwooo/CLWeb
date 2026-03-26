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
        Schema::table('department_programs', function (Blueprint $table) {
            $table->json('numbered_content')->nullable()->after('description');
            $table->string('title')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_programs', function (Blueprint $table) {
            $table->dropColumn('numbered_content');
            $table->string('title')->nullable(false)->change();
        });
    }
};
