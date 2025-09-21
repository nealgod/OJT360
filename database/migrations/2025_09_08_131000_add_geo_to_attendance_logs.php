<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->decimal('lat_in', 10, 7)->nullable()->after('photo_in_path');
            $table->decimal('lng_in', 10, 7)->nullable()->after('lat_in');
            $table->decimal('lat_out', 10, 7)->nullable()->after('photo_out_path');
            $table->decimal('lng_out', 10, 7)->nullable()->after('lat_out');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn(['lat_in','lng_in','lat_out','lng_out']);
        });
    }
};


