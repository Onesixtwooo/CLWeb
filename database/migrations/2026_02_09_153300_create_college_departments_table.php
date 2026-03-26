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
        Schema::create('college_departments', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug')->index();
            $table->string('name');
            $table->text('details')->nullable();
            $table->string('logo')->nullable();
            $table->json('sections')->nullable(); // Store department sections (overview, programs, etc.)
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('college_slug')
                  ->references('slug')
                  ->on('colleges')
                  ->onDelete('cascade');
            
            // Ensure unique department names per college
            $table->unique(['college_slug', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_departments');
    }
};
