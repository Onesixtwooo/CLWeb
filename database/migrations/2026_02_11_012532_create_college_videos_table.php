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
        Schema::create('college_videos', function (Blueprint $table) {
            $table->id();
            $table->string('college_slug');
            $table->enum('video_type', ['url', 'file']);
            $table->string('video_url', 500)->nullable();
            $table->string('video_file', 255)->nullable();
            $table->string('video_title')->nullable();
            $table->text('video_description')->nullable();
            $table->timestamps();
            
            $table->foreign('college_slug')->references('slug')->on('colleges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_videos');
    }
};
