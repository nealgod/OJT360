<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('placement_requests', 'position_title')) {
                $table->string('position_title')->nullable()->after('external_company_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            if (Schema::hasColumn('placement_requests', 'position_title')) {
                $table->dropColumn('position_title');
            }
        });
    }
};


