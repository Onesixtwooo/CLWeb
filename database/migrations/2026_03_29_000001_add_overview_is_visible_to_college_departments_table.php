<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            if (! Schema::hasColumn('college_departments', 'overview_is_visible')) {
                $table->boolean('overview_is_visible')->default(true)->after('overview_body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            if (Schema::hasColumn('college_departments', 'overview_is_visible')) {
                $table->dropColumn('overview_is_visible');
            }
        });
    }
};
