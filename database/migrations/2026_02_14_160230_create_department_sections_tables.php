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
        // 1. Add visibility columns to college_departments
        Schema::table('college_departments', function (Blueprint $table) {
            $table->boolean('awards_is_visible')->default(true);
            $table->boolean('research_is_visible')->default(true);
            $table->boolean('extension_is_visible')->default(false);
            $table->boolean('training_is_visible')->default(false);
            $table->boolean('facilities_is_visible')->default(false);
            $table->boolean('alumni_is_visible')->default(false);
        });

        // 2. Create tables for department sections
        $sectionTables = [
            'department_awards',
            'department_research',
            'department_alumni',
            'department_extensions',
            'department_trainings',
            'department_facilities',
        ];

        foreach ($sectionTables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('department_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('department_id')->references('id')->on('college_departments')->onDelete('cascade');
            });
        }

        // 3. Migrate data from sections JSON to new tables
        $departments = DB::table('college_departments')->get();
        foreach ($departments as $dept) {
            // Ensure 'sections' column exists and is not null before decoding
            if (!isset($dept->sections) || is_null($dept->sections)) {
                continue;
            }

            $sections = json_decode($dept->sections, true);
            if (empty($sections)) continue;

            $mapping = [
                'awards' => ['table' => 'department_awards', 'visibility_col' => 'awards_is_visible'],
                'research' => ['table' => 'department_research', 'visibility_col' => 'research_is_visible'],
                'extension' => ['table' => 'department_extensions', 'visibility_col' => 'extension_is_visible'],
                'training' => ['table' => 'department_trainings', 'visibility_col' => 'training_is_visible'],
                'facilities' => ['table' => 'department_facilities', 'visibility_col' => 'facilities_is_visible'],
                'alumni' => ['table' => 'department_alumni', 'visibility_col' => 'alumni_is_visible'],
            ];

            $updates = [];

            foreach ($mapping as $key => $info) {
                if (!isset($sections[$key])) continue;

                $data = $sections[$key];
                $items = $data['items'] ?? (isset($data[0]) ? $data : []); // Handle both wrapped and direct array
                $isVisible = $data['is_visible'] ?? null;

                if ($isVisible !== null) {
                    $updates[$info['visibility_col']] = $isVisible;
                }

                foreach ($items as $index => $item) {
                    if (empty($item['title'])) continue;

                    DB::table($info['table'])->insert([
                        'department_id' => $dept->id,
                        'title' => $item['title'],
                        'description' => $item['description'] ?? null,
                        'image' => $item['image'] ?? null,
                        'sort_order' => $index,
                        'created_at' => $item['created_at'] ?? now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if (!empty($updates)) {
                DB::table('college_departments')->where('id', $dept->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_facilities');
        Schema::dropIfExists('department_trainings');
        Schema::dropIfExists('department_extensions');
        Schema::dropIfExists('department_alumni');
        Schema::dropIfExists('department_research');
        Schema::dropIfExists('department_awards');

        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn([
                'awards_is_visible',
                'research_is_visible',
                'extension_is_visible',
                'training_is_visible',
                'facilities_is_visible',
                'alumni_is_visible',
            ]);
        });
    }
};
