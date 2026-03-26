<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->string('objectives_title')->nullable()->after('overview_body');
            $table->text('objectives_body')->nullable()->after('objectives_title');
        });
    }

    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn(['objectives_title', 'objectives_body']);
        });
    }
};
