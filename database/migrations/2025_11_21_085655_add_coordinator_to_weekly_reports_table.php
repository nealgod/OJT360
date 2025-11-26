<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->foreignId('coordinator_user_id')->nullable()->after('student_user_id')->constrained('users')->nullOnDelete();
            $table->text('coordinator_feedback')->nullable()->after('supervisor_reviewed_at');
            $table->timestamp('coordinator_reviewed_at')->nullable()->after('coordinator_feedback');

            $table->index('coordinator_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropForeign(['coordinator_user_id']);
            $table->dropIndex(['coordinator_user_id']);
            $table->dropColumn(['coordinator_user_id', 'coordinator_feedback', 'coordinator_reviewed_at']);
        });
    }
};
