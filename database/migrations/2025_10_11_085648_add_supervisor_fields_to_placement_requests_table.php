<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            $table->string('supervisor_name')->nullable()->after('contact_person');
            $table->string('supervisor_email')->nullable()->after('supervisor_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            $table->dropColumn(['supervisor_name', 'supervisor_email']);
        });
    }
};