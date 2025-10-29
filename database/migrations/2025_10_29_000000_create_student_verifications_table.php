<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('student_verifications', function (Blueprint $table) {
			$table->id();
			$table->string('student_id');
			$table->string('token')->unique();
			$table->timestamp('expires_at');
			$table->timestamps();
			$table->index('student_id');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('student_verifications');
	}
};


