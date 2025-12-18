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
            // New 4-Log Time Columns
            $table->time('am_in_time')->nullable()->after('work_date');
            $table->time('am_out_time')->nullable()->after('am_in_time');
            $table->time('pm_in_time')->nullable()->after('am_out_time');
            $table->time('pm_out_time')->nullable()->after('pm_in_time');

            // New Geolocation Columns (Specific for each punch)
            // Using decimal(10, 8) for high precision GPS
            $table->decimal('am_in_lat', 11, 8)->nullable()->after('status');
            $table->decimal('am_in_lng', 11, 8)->nullable()->after('am_in_lat');
            
            $table->decimal('am_out_lat', 11, 8)->nullable()->after('am_in_lng');
            $table->decimal('am_out_lng', 11, 8)->nullable()->after('am_out_lat');

            $table->decimal('pm_in_lat', 11, 8)->nullable()->after('am_out_lng');
            $table->decimal('pm_in_lng', 11, 8)->nullable()->after('pm_in_lat');

            $table->decimal('pm_out_lat', 11, 8)->nullable()->after('pm_in_lng');
            $table->decimal('pm_out_lng', 11, 8)->nullable()->after('pm_out_lat');

            // New Photo Columns (One for each punch)
            $table->string('am_in_photo')->nullable()->after('pm_out_lng');
            $table->string('am_out_photo')->nullable()->after('am_in_photo');
            $table->string('pm_in_photo')->nullable()->after('am_out_photo');
            $table->string('pm_out_photo')->nullable()->after('pm_in_photo');
            
            // Note: We keep original time_in, time_out, lat_in, lat_out as legacy/legacy support
            // or we could use them for "Summary" purposes.
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
            $table->dropColumn([
                'am_in_time', 'am_out_time', 'pm_in_time', 'pm_out_time',
                'am_in_lat', 'am_in_lng',
                'am_out_lat', 'am_out_lng',
                'pm_in_lat', 'pm_in_lng',
                'pm_out_lat', 'pm_out_lng'
            ]);
        });
    }
};
