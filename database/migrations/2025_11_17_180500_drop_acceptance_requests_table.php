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
        // Drop acceptance_requests table - no longer used
        // New flow: Supervisors directly search and accept students
        Schema::dropIfExists('acceptance_requests');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate table if rollback is needed
        Schema::create('acceptance_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_user_id');
            $table->string('supervisor_email');
            $table->string('supervisor_name')->nullable();
            $table->string('company_name');
            $table->string('company_address')->nullable();
            $table->string('position');
            $table->text('message')->nullable();
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->enum('status', ['pending', 'completed', 'expired', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->foreign('student_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
