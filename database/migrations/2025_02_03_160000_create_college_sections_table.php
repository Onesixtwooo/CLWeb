<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('college_sections', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug', 80);
            $table->string('section_slug', 80);
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();

            $table->unique(['college_slug', 'section_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('college_sections');
    }
};
