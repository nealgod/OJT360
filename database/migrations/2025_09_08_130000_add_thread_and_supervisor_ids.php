<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('thread_id')->nullable()->after('decided_at');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('student_profiles', 'supervisor_id')) {
                $table->foreignId('supervisor_id')->nullable()->after('assigned_company_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            $table->dropColumn('thread_id');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('student_profiles', 'supervisor_id')) {
                $table->dropConstrainedForeignId('supervisor_id');
            }
        });
    }
};


