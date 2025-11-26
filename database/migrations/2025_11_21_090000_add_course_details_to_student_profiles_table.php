<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('student_profiles', 'year_level')) {
                $table->string('year_level')->nullable()->after('course');
            }

            if (! Schema::hasColumn('student_profiles', 'section')) {
                $table->string('section', 10)->nullable()->after('year_level');
            }

            if (! Schema::hasColumn('student_profiles', 'course_section_code')) {
                $table->string('course_section_code')->nullable()->after('section');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('student_profiles', 'course_section_code')) {
                $table->dropColumn('course_section_code');
            }

            if (Schema::hasColumn('student_profiles', 'section')) {
                $table->dropColumn('section');
            }

            if (Schema::hasColumn('student_profiles', 'year_level')) {
                $table->dropColumn('year_level');
            }
        });
    }
};
