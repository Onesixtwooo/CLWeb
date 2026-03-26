<?php

use App\Models\CollegeDepartment;
use App\Models\CollegeSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate departments from college_sections.meta to college_departments table
        $departmentSections = CollegeSection::where('section_slug', 'departments')
            ->whereNotNull('meta')
            ->where('meta', '!=', '')
            ->get();

        foreach ($departmentSections as $section) {
            $metaData = json_decode($section->meta, true);
            
            if (is_array($metaData) && isset($metaData['departments'])) {
                $departments = $metaData['departments'];
                
                foreach ($departments as $index => $dept) {
                    // Create department record
                    CollegeDepartment::create([
                        'college_slug' => $section->college_slug,
                        'name' => $dept['name'] ?? 'Unnamed Department',
                        'details' => $dept['details'] ?? null,
                        'logo' => $dept['logo'] ?? null,
                        'sections' => $dept['sections'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
                
                echo "Migrated " . count($departments) . " departments for college: {$section->college_slug}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Move departments back to college_sections.meta
        $colleges = DB::table('college_departments')
            ->select('college_slug')
            ->distinct()
            ->pluck('college_slug');

        foreach ($colleges as $collegeSlug) {
            $departments = CollegeDepartment::where('college_slug', $collegeSlug)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($dept) {
                    return [
                        'name' => $dept->name,
                        'details' => $dept->details,
                        'logo' => $dept->logo,
                        'sections' => $dept->sections,
                    ];
                })
                ->toArray();

            CollegeSection::updateOrCreate(
                [
                    'college_slug' => $collegeSlug,
                    'section_slug' => 'departments',
                ],
                [
                    'title' => 'Departments',
                    'meta' => json_encode(['departments' => $departments]),
                ]
            );
        }

        // Clear the college_departments table
        DB::table('college_departments')->truncate();
    }
};
