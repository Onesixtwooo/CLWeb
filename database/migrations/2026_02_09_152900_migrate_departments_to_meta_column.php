<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate departments from body column to meta column
        $departmentSections = DB::table('college_sections')
            ->where('section_slug', 'departments')
            ->whereNotNull('body')
            ->where('body', '!=', '')
            ->get();

        foreach ($departmentSections as $section) {
            // Check if body contains JSON with departments
            $bodyData = json_decode($section->body, true);
            
            if (is_array($bodyData) && isset($bodyData['departments'])) {
                // Check if meta is empty or null
                $metaData = json_decode($section->meta, true);
                
                if (empty($metaData) || !isset($metaData['departments'])) {
                    // Move departments from body to meta
                    DB::table('college_sections')
                        ->where('id', $section->id)
                        ->update([
                            'meta' => $section->body,
                            'body' => null, // Clear the body column
                        ]);
                    
                    echo "Migrated departments for college: {$section->college_slug}\n";
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: move departments from meta back to body
        $departmentSections = DB::table('college_sections')
            ->where('section_slug', 'departments')
            ->whereNotNull('meta')
            ->where('meta', '!=', '')
            ->get();

        foreach ($departmentSections as $section) {
            $metaData = json_decode($section->meta, true);
            
            if (is_array($metaData) && isset($metaData['departments'])) {
                DB::table('college_sections')
                    ->where('id', $section->id)
                    ->update([
                        'body' => $section->meta,
                        'meta' => null,
                    ]);
            }
        }
    }
};
