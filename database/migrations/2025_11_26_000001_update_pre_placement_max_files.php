<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure all pre-placement requirements only allow a single file
        DB::table('document_requirements')
            ->where('type', 'pre_placement')
            ->update(['max_files_per_submission' => 1]);

        // Set the default to 1 so future requirements follow the same rule
        DB::statement('ALTER TABLE document_requirements MODIFY max_files_per_submission INT DEFAULT 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the previous default (2) for backwards compatibility
        DB::statement('ALTER TABLE document_requirements MODIFY max_files_per_submission INT DEFAULT 2');
    }
};
