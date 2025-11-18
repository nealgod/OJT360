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
        Schema::table('resumes', function (Blueprint $table) {
            $table->boolean('submitted_to_documents')->default(false)->after('profile_image');
            $table->timestamp('submitted_at')->nullable()->after('submitted_to_documents');
        });

        Schema::table('application_letters', function (Blueprint $table) {
            $table->boolean('submitted_to_documents')->default(false)->after('content');
            $table->timestamp('submitted_at')->nullable()->after('submitted_to_documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn(['submitted_to_documents', 'submitted_at']);
        });

        Schema::table('application_letters', function (Blueprint $table) {
            $table->dropColumn(['submitted_to_documents', 'submitted_at']);
        });
    }
};
