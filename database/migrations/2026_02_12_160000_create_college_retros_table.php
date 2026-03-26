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
        Schema::create('college_retros', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug');
            $table->string('title')->nullable(); // retro_title
            $table->text('description')->nullable(); // retro_description
            $table->string('stamp')->nullable(); // retro_stamp or badge
            $table->string('background_image')->nullable(); // hero_background_image
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->foreign('college_slug')->references('slug')->on('colleges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_retros');
    }
};
