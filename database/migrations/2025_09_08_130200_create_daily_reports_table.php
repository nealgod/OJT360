<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('work_date');
            $table->text('summary');
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['submitted', 'returned', 'approved'])->default('submitted');
            $table->timestamps();
            $table->unique(['student_user_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};


