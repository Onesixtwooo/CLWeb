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
            $table->string('overview_title')->default('Overview')->nullable()->after('name');
            $table->longText('overview_body')->nullable()->after('overview_title');
        });

        // Migrate existing data for title/body
        $departments = \DB::table('college_departments')->get();
        foreach ($departments as $dept) {
            if ($dept->sections) {
                $sections = json_decode($dept->sections, true);
                $overview = $sections['overview'] ?? [];

                // Use existing data or fallbacks
                $title = $overview['title'] ?? 'Overview';
                $body = $overview['body'] ?? null;
                // If body is empty, maybe try to migrate details if appropriate?
                // But details is a separate column. I'll just stick to overview.body.

                \DB::table('college_departments')
                    ->where('id', $dept->id)
                    ->update([
                        'overview_title' => $title,
                        'overview_body' => $body,
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
            $table->dropColumn(['overview_title', 'overview_body']);
        });
    }
};
