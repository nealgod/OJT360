<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrate legacy data to new quad-logging fields
        DB::table('attendance_logs')->whereNull('am_in_time')->whereNotNull('time_in')->chunkById(100, function ($logs) {
            foreach ($logs as $log) {
                DB::table('attendance_logs')
                    ->where('id', $log->id)
                    ->update([
                        'am_in_time' => $log->time_in,
                        'am_out_time' => $log->time_out,
                        'am_in_photo' => $log->photo_in_path,
                        'am_out_photo' => $log->photo_out_path,
                        'am_in_lat' => $log->lat_in,
                        'am_in_lng' => $log->lng_in,
                        'am_out_lat' => $log->lat_out,
                        'am_out_lng' => $log->lng_out,
                    ]);
            }
        });

        // 2. Drop legacy columns
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'time_in',
                'time_out',
                'photo_in_path',
                'photo_out_path',
                'lat_in',
                'lng_in',
                'lat_out',
                'lng_out'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->string('time_in')->nullable();
            $table->string('time_out')->nullable();
            $table->string('photo_in_path')->nullable();
            $table->string('photo_out_path')->nullable();
            $table->decimal('lat_in', 10, 8)->nullable();
            $table->decimal('lng_in', 11, 8)->nullable();
            $table->decimal('lat_out', 10, 8)->nullable();
            $table->decimal('lng_out', 11, 8)->nullable();
        });

        // Data restoration is complex and usually skipped for "drop" operations, 
        // but since we keep am_in/out, it's possible if needed.
    }
};
