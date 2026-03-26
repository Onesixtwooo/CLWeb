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
        // 1. Create the new department_curricula table
        Schema::create('department_curricula', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->string('title');
            $table->json('courses'); // Array of course names
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('college_departments')->onDelete('cascade');
        });

        // 2. Migrate curriculum data from sections JSON
        $departments = DB::table('college_departments')->whereNotNull('sections')->get();
        
        foreach ($departments as $dept) {
            $sections = json_decode($dept->sections, true);
            
            if (isset($sections['curriculum']) && is_array($sections['curriculum'])) {
                foreach ($sections['curriculum'] as $index => $category) {
                    if (empty($category['title'])) continue;

                    DB::table('department_curricula')->insert([
                        'department_id' => $dept->id,
                        'title' => $category['title'],
                        'courses' => json_encode($category['courses'] ?? []),
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3. Drop the deprecated sections column
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn('sections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->json('sections')->nullable();
        });

        Schema::dropIfExists('department_curricula');
    }
};
