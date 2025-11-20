<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('student_profiles', 'program_id')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->foreignId('program_id')
                    ->nullable()
                    ->after('department')
                    ->constrained()
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('student_profiles') && Schema::hasTable('enrollment_whitelists')) {
            DB::statement('
                UPDATE student_profiles sp
                INNER JOIN enrollment_whitelists ew ON ew.student_id = sp.student_id
                SET sp.program_id = ew.program_id
                WHERE sp.program_id IS NULL AND ew.program_id IS NOT NULL
            ');
        }

        if (Schema::hasTable('student_profiles') && Schema::hasTable('programs')) {
            DB::statement('
                UPDATE student_profiles sp
                INNER JOIN programs p ON p.name = sp.course
                SET sp.program_id = p.id
                WHERE sp.program_id IS NULL
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('student_profiles', 'program_id')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->dropConstrainedForeignId('program_id');
            });
        }
    }
};

