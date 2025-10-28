<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_whitelists', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->index();
            $table->string('name');
            $table->string('contact_number')->nullable();
            $table->unsignedBigInteger('program_id')->nullable()->index();
            $table->string('email')->index();
            $table->unsignedBigInteger('department_id')->nullable()->index();
            $table->enum('status', ['pending', 'activated', 'archived'])->default('pending')->index();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_whitelists');
    }
};


