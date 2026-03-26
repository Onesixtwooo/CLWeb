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
        $tables = [
            'department_awards',
            'department_research',
            'department_alumni',
            'department_extensions',
            'department_trainings',
            'department_facilities',
            'department_programs',
            'department_curricula',
            'department_objectives',
            'department_outcomes',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('department_id')->nullable()->change();
                $table->unsignedBigInteger('institute_id')->nullable()->after('department_id');
                
                $table->foreign('institute_id')->references('id')->on('college_institutes')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'department_awards',
            'department_research',
            'department_alumni',
            'department_extensions',
            'department_trainings',
            'department_facilities',
            'department_programs',
            'department_curricula',
            'department_objectives',
            'department_outcomes',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign([ 'institute_id' ]);
                $table->dropColumn('institute_id');
                $table->unsignedBigInteger('department_id')->nullable(false)->change();
            });
        }
    }
};
