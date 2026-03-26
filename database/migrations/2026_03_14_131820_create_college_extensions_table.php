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
        Schema::create('college_extensions', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug', 80)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_draft')->default(false);
            $table->timestamp('publish_at')->nullable();
            $table->timestamps();
        });

        // Migrate existing JSON data from college_sections
        $sections = \App\Models\CollegeSection::where('section_slug', 'extension')->get();
        foreach ($sections as $section) {
            if (!$section->meta) continue;
            $meta = $section->meta;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            $items = $meta['items'] ?? [];
            foreach ($items as $index => $item) {
                \App\Models\CollegeExtension::create([
                    'college_slug' => $section->college_slug,
                    'title' => $item['title'] ?? 'Untitled',
                    'description' => $item['description'] ?? null,
                    'image' => $item['image'] ?? null,
                    'is_visible' => $section->is_visible,
                    'is_draft' => $section->is_draft,
                    'publish_at' => $section->publish_at,
                    'sort_order' => $index,
                    'created_at' => $item['created_at'] ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_extensions');
    }
};
