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
        Schema::create('facebook_configs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // 'college', 'department', 'organization', 'global'
            $table->string('entity_id')->nullable(); // college_slug, department_id, or organization_id
            $table->string('page_name')->nullable(); // Display name
            $table->string('page_id');
            $table->text('access_token');
            $table->boolean('is_active')->default(true);
            $table->integer('fetch_limit')->default(5); // Posts to fetch per run
            $table->string('article_category')->nullable(); // Custom category for articles
            $table->string('article_author')->nullable(); // Custom author name
            $table->timestamps();

            $table->unique(['entity_type', 'entity_id']);
            $table->index('entity_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_configs');
    }
};
