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
        Schema::table('college_institutes', function (Blueprint $table) {
            $table->text('history')->nullable()->after('overview_body');
        });

        Schema::table('faculty', function (Blueprint $table) {
            $table->unsignedBigInteger('institute_id')->nullable()->after('college_slug');
            $table->foreign('institute_id')->references('id')->on('college_institutes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_institutes', function (Blueprint $table) {
            $table->dropColumn('history');
        });

        Schema::table('faculty', function (Blueprint $table) {
            $table->dropForeign(['institute_id']);
            $table->dropColumn('institute_id');
        });
    }
};
