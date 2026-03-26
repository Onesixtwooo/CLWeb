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
        Schema::table('college_departments', function (Blueprint $table) {
            $table->longText('program_description')->nullable()->after('details');
            $table->longText('graduate_outcomes')->nullable()->after('program_description');
            $table->string('graduate_outcomes_image')->nullable()->after('graduate_outcomes');
            $table->string('banner_image')->nullable()->after('graduate_outcomes_image');
            $table->json('banner_images')->nullable()->after('banner_image');
            $table->string('card_image')->nullable()->after('banner_images');
            $table->string('social_facebook')->nullable()->after('card_image');
            $table->string('social_x')->nullable()->after('social_facebook');
            $table->string('social_youtube')->nullable()->after('social_x');
            $table->string('social_linkedin')->nullable()->after('social_youtube');
            $table->string('social_instagram')->nullable()->after('social_linkedin');
            $table->string('social_other')->nullable()->after('social_instagram');
        });

        // Migrate existing data
        $departments = \DB::table('college_departments')->get();
        foreach ($departments as $dept) {
            if ($dept->sections) {
                $sections = json_decode($dept->sections, true);
                $overview = $sections['overview'] ?? [];

                \DB::table('college_departments')
                    ->where('id', $dept->id)
                    ->update([
                        'program_description' => $overview['program_description'] ?? null,
                        'graduate_outcomes' => $overview['graduate_outcomes'] ?? null,
                        'graduate_outcomes_image' => $overview['graduate_outcomes_image'] ?? null,
                        'banner_image' => $overview['banner_image'] ?? null,
                        'banner_images' => isset($overview['banner_images']) ? json_encode($overview['banner_images']) : null,
                        'card_image' => $overview['card_image'] ?? null,
                        'social_facebook' => $overview['social_facebook'] ?? null,
                        'social_x' => $overview['social_x'] ?? null,
                        'social_youtube' => $overview['social_youtube'] ?? null,
                        'social_linkedin' => $overview['social_linkedin'] ?? null,
                        'social_instagram' => $overview['social_instagram'] ?? null,
                        'social_other' => $overview['social_other'] ?? null,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_departments', function (Blueprint $table) {
            $table->dropColumn([
                'program_description',
                'graduate_outcomes',
                'graduate_outcomes_image',
                'banner_image',
                'banner_images',
                'card_image',
                'social_facebook',
                'social_x',
                'social_youtube',
                'social_linkedin',
                'social_instagram',
                'social_other',
            ]);
        });
    }
};
