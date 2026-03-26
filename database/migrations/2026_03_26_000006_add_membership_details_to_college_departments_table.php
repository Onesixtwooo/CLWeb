<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->boolean('membership_is_visible')->default(true)->after('facilities_is_visible');
            $table->string('membership_title')->nullable()->after('facilities_body');
            $table->text('membership_body')->nullable()->after('membership_title');
        });
    }

    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn([
                'membership_is_visible',
                'membership_title',
                'membership_body',
            ]);
        });
    }
};
