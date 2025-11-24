<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_evaluations', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('student_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('coordinator_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Document Control
            $table->string('control_number')->unique();
            $table->integer('revision_number')->default(1);
            
            // Student Information (auto-filled from profile/acceptance letter)
            $table->string('student_name');
            $table->string('student_id');
            $table->string('course')->nullable();
            $table->string('department')->nullable();
            $table->string('hte_name'); // Host Training Establishment (Company)
            $table->text('hte_address')->nullable();
            $table->date('internship_start_date')->nullable();
            $table->date('internship_end_date')->nullable();
            $table->decimal('total_hours_rendered', 8, 2)->nullable();
            
            // 7 Rating Criteria (percentage-based, not 1-5 scale)
            $table->decimal('rating_quality_thoroughness', 5, 2)->nullable(); // Max 20%
            $table->decimal('rating_dependability', 5, 2)->nullable(); // Max 15%
            $table->decimal('rating_quality_completion', 5, 2)->nullable(); // Max 20%
            $table->decimal('rating_attendance', 5, 2)->nullable(); // Max 15%
            $table->decimal('rating_cooperation', 5, 2)->nullable(); // Max 10%
            $table->decimal('rating_judgement', 5, 2)->nullable(); // Max 10%
            $table->decimal('rating_personality', 5, 2)->nullable(); // Max 5%
            
            // Calculated Total
            $table->decimal('total_rating', 5, 2)->nullable(); // Sum of all ratings (max 95%)
            
            // Comments
            $table->text('comments_recommendations')->nullable();
            
            // Signatures & Dates
            $table->string('supervisor_name');
            $table->date('supervisor_signature_date')->nullable();
            $table->boolean('student_confirmed')->default(false);
            $table->date('student_signature_date')->nullable();
            
            // Status Tracking
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            
            // Ensure only ONE final evaluation per student
            $table->unique('student_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('final_evaluations');
    }
};
