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
        Schema::table('college_departments', function (Blueprint $table) {
            $table->string('linkages_title')->nullable()->after('overview_body');
            $table->text('linkages_body')->nullable()->after('linkages_title');
            $table->boolean('linkages_is_visible')->default(true)->after('linkages_body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn(['linkages_title', 'linkages_body', 'linkages_is_visible']);
        });
    }
};
