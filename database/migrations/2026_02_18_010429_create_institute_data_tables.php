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
        // Goals
        Schema::create('institute_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained('college_institutes')->onDelete('cascade');
            $table->text('content');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Staff
        Schema::create('institute_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained('college_institutes')->onDelete('cascade');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('photo')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Research
        Schema::create('institute_research', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained('college_institutes')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Extension
        Schema::create('institute_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained('college_institutes')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Facilities
        Schema::create('institute_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained('college_institutes')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institute_facilities');
        Schema::dropIfExists('institute_extensions');
        Schema::dropIfExists('institute_research');
        Schema::dropIfExists('institute_staff');
        Schema::dropIfExists('institute_goals');
    }
};
