<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_evaluations', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supervisor_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('coordinator_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Evaluation Period
            $table->integer('evaluation_month');           // 1-12
            $table->integer('evaluation_year');            // 2025, 2026, etc.
            $table->integer('month_number');               // 1st, 2nd, 3rd month of internship

            // Auto-populated Fields
            $table->string('student_name');
            $table->string('hte_name');                    // Host Training Establishment
            $table->text('hte_address')->nullable();
            $table->text('work_assignment')->nullable();
            $table->string('work_schedule')->nullable();
            $table->string('supervisor_name');

            // RELATED SKILLS AND COMPETENCIES (Rows 1-5)
            $table->tinyInteger('rating_row_1')->nullable();   // Analytical Skills
            $table->tinyInteger('rating_row_2')->nullable();   // Communicative Competence
            $table->tinyInteger('rating_row_3')->nullable();   // Leadership Skills
            $table->tinyInteger('rating_row_4')->nullable();   // Organizational and Time Management Skills
            $table->tinyInteger('rating_row_5')->nullable();   // Technical Competence

            // QUALITY OF WORK (Rows 6-10)
            $table->tinyInteger('rating_row_6')->nullable();   // Accuracy and Dependability
            $table->tinyInteger('rating_row_7')->nullable();   // Creativity
            $table->tinyInteger('rating_row_8')->nullable();   // Multi-Tasking Ability
            $table->tinyInteger('rating_row_9')->nullable();   // Productivity/Work Speed
            $table->tinyInteger('rating_row_10')->nullable();  // Professionalism

            // WORK APPROACH (Rows 11-15)
            $table->tinyInteger('rating_row_11')->nullable();  // Adaptability to Change
            $table->tinyInteger('rating_row_12')->nullable();  // Attendance and Punctuality
            $table->tinyInteger('rating_row_13')->nullable();  // Courtesy and Respect towards Superiors & Clients
            $table->tinyInteger('rating_row_14')->nullable();  // Professional Grooming and Appearance
            $table->tinyInteger('rating_row_15')->nullable();  // Teamwork/Collaborative Qualities

            // JOB INTEREST AND COOPERATION (Rows 16-20)
            $table->tinyInteger('rating_row_16')->nullable();  // Adherence to HTE Policies and Standards
            $table->tinyInteger('rating_row_17')->nullable();  // Attitude towards Work
            $table->tinyInteger('rating_row_18')->nullable();  // Capacity to Work with Colleagues
            $table->tinyInteger('rating_row_19')->nullable();  // Initiative
            $table->tinyInteger('rating_row_20')->nullable();  // Participation in HTE Initiated Activities

            // Comments
            $table->text('comments_recommendations')->nullable();

            // Status
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');

            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['student_user_id', 'evaluation_year', 'evaluation_month'], 'unique_student_month');
            $table->index('supervisor_user_id');
            $table->index('coordinator_user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_evaluations');
    }
};
