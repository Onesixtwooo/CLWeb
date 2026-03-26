<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->string('awards_title')->nullable()->after('awards_is_visible');
            $table->text('awards_body')->nullable()->after('awards_title');
        });
    }

    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn(['awards_title', 'awards_body']);
        });
    }
};
