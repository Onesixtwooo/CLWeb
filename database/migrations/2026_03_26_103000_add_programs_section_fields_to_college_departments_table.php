<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->string('programs_title')->nullable()->after('programs_is_visible');
            $table->text('programs_body')->nullable()->after('programs_title');
        });
    }

    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn(['programs_title', 'programs_body']);
        });
    }
};
