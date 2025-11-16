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
        Schema::create('student_application_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->onDelete('cascade');
            $table->string('application_letter_path')->nullable();
            $table->string('resume_path')->nullable();
            $table->foreignId('resume_id')->nullable()->constrained('resumes')->onDelete('set null');
            $table->timestamps();
            
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
        Schema::dropIfExists('student_application_materials');
    }
};
