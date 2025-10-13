<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('placement_requests', 'start_date')) {
                $table->date('start_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('placement_requests', 'shift_start')) {
                $table->time('shift_start')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('placement_requests', 'shift_end')) {
                $table->time('shift_end')->nullable()->after('shift_start');
            }
            if (!Schema::hasColumn('placement_requests', 'break_minutes')) {
                $table->unsignedSmallInteger('break_minutes')->default(60)->after('shift_end');
            }
            if (!Schema::hasColumn('placement_requests', 'working_days')) {
                // JSON working days e.g. {"mon":true, "tue":true, ...}
                $table->json('working_days')->nullable()->after('break_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'shift_start',
                'shift_end',
                'break_minutes',
                'working_days',
            ]);
        });
    }
};


