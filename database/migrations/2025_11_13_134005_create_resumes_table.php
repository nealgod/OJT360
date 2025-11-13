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
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('personal_info')->nullable(); // name, email, phone, address, etc.
            $table->text('objective')->nullable();
            $table->json('education')->nullable(); // array of education entries
            $table->json('work_experience')->nullable(); // array of work experiences
            $table->json('skills')->nullable(); // array of skills
            $table->json('certifications')->nullable(); // array of certifications
            $table->json('references')->nullable(); // array of references
            $table->string('template_path')->default('resume-templates/template.pdf');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resumes');
    }
};
