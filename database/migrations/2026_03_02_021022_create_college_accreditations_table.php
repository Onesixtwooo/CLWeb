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
        Schema::create('college_accreditations', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug')->index();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->string('agency'); // e.g., CHED, AACCUP
            $table->string('level');  // e.g., Level IV Re-accredited
            $table->date('valid_until')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('department_programs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_accreditations');
    }
};
