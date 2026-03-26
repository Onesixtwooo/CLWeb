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
        // Move departments from body to meta for all college sections with section_slug = 'departments'
        $sections = DB::table('college_sections')
            ->where('section_slug', 'departments')
            ->get();

        foreach ($sections as $section) {
            if (!empty($section->body)) {
                $decoded = json_decode($section->body, true);
                
                // If body contains departments array, move it to meta
                if (is_array($decoded) && isset($decoded['departments'])) {
                    $departments = $decoded['departments'];
                    
                    // Update: move departments to meta, clear body
                    DB::table('college_sections')
                        ->where('id', $section->id)
                        ->update([
                            'meta' => json_encode(['departments' => $departments]),
                            'body' => '', // Clear body for section content
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Move departments back from meta to body
        $sections = DB::table('college_sections')
            ->where('section_slug', 'departments')
            ->whereNotNull('meta')
            ->get();

        foreach ($sections as $section) {
            if (!empty($section->meta)) {
                $decoded = json_decode($section->meta, true);
                
                if (is_array($decoded) && isset($decoded['departments'])) {
                    // Move back to body
                    DB::table('college_sections')
                        ->where('id', $section->id)
                        ->update([
                            'body' => json_encode($decoded),
                            'meta' => null,
                        ]);
                }
            }
        }
    }
};
