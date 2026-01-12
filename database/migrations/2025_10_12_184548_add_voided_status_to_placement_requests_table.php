<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('placement_requests')) {
            // For MySQL, we need to modify the enum column
            DB::statement("ALTER TABLE placement_requests MODIFY COLUMN status ENUM('pending', 'approved', 'declined', 'voided') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE placement_requests MODIFY COLUMN status ENUM('pending', 'approved', 'declined') DEFAULT 'pending'");
    }
};
