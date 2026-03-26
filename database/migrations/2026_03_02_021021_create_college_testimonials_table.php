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
        Schema::create('college_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug')->index();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('degree')->nullable();
            $table->text('quote');
            $table->string('photo')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_testimonials');
    }
};
