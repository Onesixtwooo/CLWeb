<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Populate existing slugs
        $facilities = DB::table('facilities')->get();
        foreach ($facilities as $facility) {
            $slug = Str::slug($facility->name);
            // Ensure uniqueness
            $originalSlug = $slug;
            $count = 1;
            while (DB::table('facilities')->where('slug', $slug)->where('id', '!=', $facility->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            
            DB::table('facilities')
                ->where('id', $facility->id)
                ->update(['slug' => $slug]);
        }

        // Now make it not nullable and unique
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
