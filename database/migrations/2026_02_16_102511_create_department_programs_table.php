<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add visibility column to college_departments
        Schema::table('college_departments', function (Blueprint $table) {
            $table->boolean('programs_is_visible')->default(true)->after('overview_body');
        });

        // 2. Create department_programs table
        Schema::create('department_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('college_departments')->onDelete('cascade');
        });

        // 3. Migrate data from college_departments.program_description to department_programs
        $departments = DB::table('college_departments')->get();
        foreach ($departments as $dept) {
            if (!empty($dept->program_description)) {
                DB::table('department_programs')->insert([
                    'department_id' => $dept->id,
                    'title' => 'Program Description', // Default title for existing data
                    'description' => $dept->program_description,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_programs');

        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn('programs_is_visible');
        });
    }
};
