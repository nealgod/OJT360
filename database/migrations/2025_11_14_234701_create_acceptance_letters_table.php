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
        Schema::create('acceptance_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acceptance_request_id')->nullable()->constrained('acceptance_requests')->onDelete('cascade');
            $table->foreignId('student_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            
            // Letter details
            $table->string('job_title');
            $table->string('department')->nullable();
            $table->string('immediate_supervisor');
            $table->date('start_date'); // Effective date
            $table->date('end_date')->nullable(); // Not used anymore
            $table->integer('total_hours');
            $table->json('work_schedule'); // {monday: "8:00-17:00", ...}
            
            // Signature
            $table->enum('signature_type', ['typed', 'uploaded', 'drawn']);
            $table->text('signature_data')->nullable(); // Base64 for drawn, path for uploaded, text for typed
            
            // Additional
            $table->text('additional_notes')->nullable();
            $table->string('letter_path');
            $table->string('document_id')->unique(); // ACC-2025-001234
            
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();
            
            $table->index('student_user_id');
            $table->index('supervisor_user_id');
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acceptance_letters');
    }
};
