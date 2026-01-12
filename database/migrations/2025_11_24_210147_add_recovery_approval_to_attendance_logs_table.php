<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('attendance_logs', 'recovery_approved')) {
                $table->boolean('recovery_approved')->nullable()->after('recovery_reason');
            }
            if (! Schema::hasColumn('attendance_logs', 'recovery_approved_at')) {
                $table->timestamp('recovery_approved_at')->nullable()->after('recovery_approved');
            }
            if (! Schema::hasColumn('attendance_logs', 'recovery_approved_by')) {
                $table->foreignId('recovery_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('recovery_approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['recovery_approved_by']);
            $table->dropColumn(['recovery_approved', 'recovery_approved_at', 'recovery_approved_by']);
        });
    }
};
