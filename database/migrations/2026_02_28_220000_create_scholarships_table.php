<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug', 80)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('requirements')->nullable();
            $table->text('process')->nullable();
            $table->text('benefits')->nullable();
            $table->string('image')->nullable();
            $table->string('added_by', 20)->default('admin'); // 'superadmin' or 'admin'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Migrate existing JSON data from college_sections
        $sections = \App\Models\CollegeSection::where('section_slug', 'scholarships')->get();
        foreach ($sections as $section) {
            if (!$section->meta) continue;
            $meta = json_decode($section->meta, true);
            $items = $meta['items'] ?? [];
            foreach ($items as $index => $item) {
                \App\Models\Scholarship::create([
                    'college_slug' => $section->college_slug,
                    'title' => $item['title'] ?? 'Untitled',
                    'description' => $item['description'] ?? null,
                    'qualifications' => $item['qualifications'] ?? null,
                    'requirements' => $item['requirements'] ?? null,
                    'process' => $item['process'] ?? null,
                    'benefits' => $item['benefits'] ?? null,
                    'image' => $item['image'] ?? null,
                    'added_by' => 'admin',
                    'sort_order' => $index,
                    'created_at' => $item['created_at'] ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
