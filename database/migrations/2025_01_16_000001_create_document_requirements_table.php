<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Parent Consent Form", "Medical Certificate"
            $table->text('description')->nullable();
            $table->enum('type', ['pre_placement', 'post_placement', 'ongoing']);
            $table->boolean('is_required')->default(true);
            $table->json('file_types')->nullable(); // ['pdf', 'jpg', 'docx']
            $table->integer('max_file_size_mb')->default(10);
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requirements');
    }
};
