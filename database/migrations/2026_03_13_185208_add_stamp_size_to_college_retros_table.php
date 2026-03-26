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
        Schema::table('college_retros', function (Blueprint $table) {
            $table->integer('stamp_size')->nullable()->after('stamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_retros', function (Blueprint $table) {
            $table->dropColumn('stamp_size');
        });
    }
};
