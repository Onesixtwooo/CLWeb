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
        Schema::table('college_institutes', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->text('details')->nullable()->after('phone');
            $table->string('logo')->nullable()->after('details');
            $table->text('program_description')->nullable()->after('logo');
            $table->text('graduate_outcomes')->nullable()->after('program_description');
            $table->string('graduate_outcomes_title')->nullable()->after('graduate_outcomes');
            $table->string('graduate_outcomes_image')->nullable()->after('graduate_outcomes_title');
            $table->string('banner_image')->nullable()->after('graduate_outcomes_image');
            $table->json('banner_images')->nullable()->after('banner_image');
            $table->string('card_image')->nullable()->after('banner_images');
            
            // Social Links
            $table->string('social_facebook')->nullable()->after('card_image');
            $table->string('social_x')->nullable()->after('social_facebook');
            $table->string('social_youtube')->nullable()->after('social_x');
            $table->string('social_linkedin')->nullable()->after('social_youtube');
            $table->string('social_instagram')->nullable()->after('social_linkedin');
            $table->string('social_other')->nullable()->after('social_instagram');
            
            // Overview Section
            $table->string('overview_title')->nullable()->after('social_other');
            $table->text('overview_body')->nullable()->after('overview_title');
            
            // Visibility Toggles
            $table->boolean('awards_is_visible')->default(true)->after('overview_body');
            $table->boolean('research_is_visible')->default(true)->after('awards_is_visible');
            $table->boolean('extension_is_visible')->default(true)->after('research_is_visible');
            $table->boolean('training_is_visible')->default(true)->after('extension_is_visible');
            $table->boolean('facilities_is_visible')->default(true)->after('training_is_visible');
            $table->boolean('alumni_is_visible')->default(true)->after('facilities_is_visible');
            $table->boolean('programs_is_visible')->default(true)->after('alumni_is_visible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_institutes', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'phone', 'details', 'logo', 'program_description',
                'graduate_outcomes', 'graduate_outcomes_title', 'graduate_outcomes_image',
                'banner_image', 'banner_images', 'card_image',
                'social_facebook', 'social_x', 'social_youtube', 'social_linkedin', 'social_instagram', 'social_other',
                'overview_title', 'overview_body',
                'awards_is_visible', 'research_is_visible', 'extension_is_visible',
                'training_is_visible', 'facilities_is_visible', 'alumni_is_visible', 'programs_is_visible'
            ]);
        });
    }
};
