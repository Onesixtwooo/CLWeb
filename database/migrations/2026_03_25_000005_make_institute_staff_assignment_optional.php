<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institute_staff', function (Blueprint $table) {
            $table->string('college_slug')->nullable()->after('institute_id');
        });

        DB::table('institute_staff')
            ->join('college_institutes', 'college_institutes.id', '=', 'institute_staff.institute_id')
            ->update([
                'institute_staff.college_slug' => DB::raw('college_institutes.college_slug'),
            ]);

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE institute_staff DROP FOREIGN KEY institute_staff_institute_id_foreign');
            DB::statement('ALTER TABLE institute_staff MODIFY institute_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE institute_staff ADD CONSTRAINT institute_staff_institute_id_foreign FOREIGN KEY (institute_id) REFERENCES college_institutes(id) ON DELETE CASCADE');
        } elseif ($driver === 'sqlite') {
            // SQLite column alteration is intentionally skipped here.
            // Local environments using SQLite should recreate this table if needed.
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE institute_staff DROP FOREIGN KEY institute_staff_institute_id_foreign');
            DB::statement('ALTER TABLE institute_staff MODIFY institute_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE institute_staff ADD CONSTRAINT institute_staff_institute_id_foreign FOREIGN KEY (institute_id) REFERENCES college_institutes(id) ON DELETE CASCADE');
        }

        Schema::table('institute_staff', function (Blueprint $table) {
            $table->dropColumn('college_slug');
        });
    }
};
