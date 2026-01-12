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
            if (! Schema::hasColumn('attendance_logs', 'is_recovered')) {
                $table->boolean('is_recovered')->default(false)->after('status');
            }
            if (! Schema::hasColumn('attendance_logs', 'recovery_reason')) {
                $table->text('recovery_reason')->nullable()->after('is_recovered');
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
            $table->dropColumn(['is_recovered', 'recovery_reason']);
        });
    }
};
