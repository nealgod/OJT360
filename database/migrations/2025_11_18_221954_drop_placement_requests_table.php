<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('placement_requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate table structure if needed to rollback
        Schema::create('placement_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->string('external_company_name')->nullable();
            $table->string('external_company_address')->nullable();
            $table->string('position_title');
            $table->date('start_date');
            $table->string('shift_start');
            $table->string('shift_end');
            $table->json('working_days')->nullable();
            $table->integer('break_minutes')->default(60);
            $table->string('contact_person');
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_email')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'approved', 'declined', 'voided'])->default('pending');
            $table->foreignId('decided_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamps();

            $table->index('student_user_id');
            $table->index('company_id');
            $table->index('status');
        });
    }
};
