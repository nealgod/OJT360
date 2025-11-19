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
        Schema::dropIfExists('student_application_materials');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate table structure if needed to rollback
        Schema::create('student_application_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->onDelete('cascade');
            $table->string('resume_path')->nullable();
            $table->string('cover_letter_path')->nullable();
            $table->timestamps();
        });
    }
};
