<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->date('work_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('photo_in_path')->nullable();
            $table->string('photo_out_path')->nullable();
            $table->unsignedSmallInteger('minutes_worked')->default(0);
            $table->enum('status', ['pending', 'approved', 'flagged'])->default('pending');
            $table->timestamps();
            $table->unique(['student_user_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};


