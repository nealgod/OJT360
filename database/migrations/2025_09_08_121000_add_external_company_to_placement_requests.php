<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            $table->string('external_company_name')->nullable()->after('company_id');
            $table->string('external_company_address')->nullable()->after('external_company_name');
        });
    }

    public function down(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            $table->dropColumn(['external_company_name', 'external_company_address']);
        });
    }
};


