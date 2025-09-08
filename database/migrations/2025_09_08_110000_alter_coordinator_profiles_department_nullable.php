<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coordinator_profiles', function (Blueprint $table) {
            $table->string('department')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('coordinator_profiles', function (Blueprint $table) {
            $table->string('department')->nullable(false)->change();
        });
    }
};


