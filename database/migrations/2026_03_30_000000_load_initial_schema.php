<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Representative tables that indicate the CMS schema already exists.
     *
     * @var array<int, string>
     */
    private array $existingSchemaMarkers = [
        'users',
        'colleges',
        'college_sections',
    ];

    /**
     * Tables created by the initial schema migration.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'cache',
        'cache_locks',
        'college_contacts',
        'college_downloads',
        'college_extensions',
        'college_faqs',
        'college_institutes',
        'college_sections',
        'college_testimonials',
        'college_trainings',
        'colleges',
        'facebook_configs',
        'failed_jobs',
        'job_batches',
        'jobs',
        'password_reset_tokens',
        'scholarships',
        'sessions',
        'settings',
        'users',
        'announcements',
        'articles',
        'college_departments',
        'college_videos',
        'events',
        'facilities',
        'faculty',
        'institute_extensions',
        'institute_facilities',
        'institute_goals',
        'institute_research',
        'institute_staff',
        'college_memberships',
        'college_organizations',
        'college_retros',
        'department_alumni',
        'department_awards',
        'department_curricula',
        'department_extensions',
        'department_facilities',
        'department_linkages',
        'department_objectives',
        'department_outcomes',
        'department_programs',
        'department_research',
        'department_trainings',
        'facility_images',
        'college_accreditations',
    ];

    public function up(): void
    {
        foreach ($this->existingSchemaMarkers as $table) {
            if (Schema::hasTable($table)) {
                return;
            }
        }

        DB::unprepared($this->schemaSql());
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach (array_reverse($this->tables) as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    private function schemaSql(): string
    {
        $path = database_path('migrations/schema/initial-schema.sql');
        $sql = file_get_contents($path);

        if ($sql === false) {
            throw new RuntimeException("Unable to read initial schema file: {$path}");
        }

        $patterns = [
            '/^DROP TABLE IF EXISTS `[^`]+`;\R/m',
            '/CREATE TABLE `migrations` \(\R.*?\R\) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\R\R/s',
            '/INSERT INTO `migrations` \(`migration`, `batch`\) VALUES\R.*?;\R\R/s',
        ];

        return (string) preg_replace($patterns, '', $sql);
    }
};
