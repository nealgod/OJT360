<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('week_start_date');
            $table->date('week_end_date');
            $table->integer('week_number')->default(1);

            $table->integer('days_present')->default(0);
            $table->integer('days_absent')->default(0);
            $table->integer('days_late')->default(0);
            $table->decimal('total_hours', 6, 2)->default(0);

            $table->json('entries')->nullable();
            $table->text('problems_encountered')->nullable();

            $table->text('supervisor_feedback')->nullable();
            $table->enum('supervisor_rating', ['excellent', 'good', 'satisfactory', 'needs_improvement'])->nullable();
            $table->timestamp('supervisor_reviewed_at')->nullable();

            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->unique(['student_user_id', 'week_start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};

