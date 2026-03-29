<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->string('curriculum_title')->nullable()->after('objectives_body');
            $table->longText('curriculum_body')->nullable()->after('curriculum_title');
        });
    }

    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn(['curriculum_title', 'curriculum_body']);
        });
    }
};
