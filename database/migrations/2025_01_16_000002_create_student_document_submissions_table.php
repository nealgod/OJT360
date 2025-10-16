<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_document_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('document_requirement_id')->constrained('document_requirements')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('file_size');
            $table->string('mime_type');
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected'])->default('submitted');
            $table->text('feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Prevent duplicate submissions for the same requirement
            $table->unique(['student_user_id', 'document_requirement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_document_submissions');
    }
};
