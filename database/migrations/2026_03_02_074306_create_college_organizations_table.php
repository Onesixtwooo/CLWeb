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
        Schema::create('college_organizations', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug')->index();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('name');
            $table->string('acronym')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('adviser')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('college_slug')->references('slug')->on('colleges')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('college_departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_organizations');
    }
};
