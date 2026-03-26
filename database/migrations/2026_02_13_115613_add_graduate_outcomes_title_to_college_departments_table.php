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
            $table->string('graduate_outcomes_title')->nullable()->after('graduate_outcomes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn('graduate_outcomes_title');
        });
    }
};
