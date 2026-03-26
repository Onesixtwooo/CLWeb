<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->string('training_title')->nullable()->after('training_is_visible');
            $table->text('training_body')->nullable()->after('training_title');
        });
    }

    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn(['training_title', 'training_body']);
        });
    }
};
