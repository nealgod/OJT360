<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coordinator_profiles', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('employee_id')->constrained('departments')->nullOnDelete();
            $table->foreignId('program_id')->nullable()->after('department_id')->constrained('programs')->nullOnDelete();
            $table->string('employee_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('coordinator_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
            $table->dropConstrainedForeignId('department_id');
            $table->string('employee_id')->nullable(false)->change();
        });
    }
};


