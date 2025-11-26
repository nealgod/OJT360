<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove deprecated post-placement requirements
        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->whereIn('name', ['Documentation Report', 'Pertinent Documents'])
            ->delete();

        // Default all existing requirements to a single file
        DB::table('document_requirements')->update([
            'max_files_per_submission' => 1,
        ]);

        // Restore specific allowances
        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->where('name', 'Weekly Accomplishment Report')
            ->update(['max_files_per_submission' => 50]);

        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->where('name', 'Photo Documentation')
            ->update(['max_files_per_submission' => 50]);

        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->where('name', 'Other Documents Not Specified')
            ->update(['max_files_per_submission' => 5]);

        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->where('name', 'Authenticated Copy of DTR')
            ->update(['max_files_per_submission' => 10]);
    }

    public function down(): void
    {
        // Recreate the removed requirements (basic defaults)
        DB::table('document_requirements')->insert([
            [
                'name' => 'Documentation Report',
                'description' => 'Comprehensive documentation of OJT activities and achievements',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => json_encode(['pdf', 'doc', 'docx']),
                'max_file_size_mb' => 10,
                'max_files_per_submission' => 1,
                'instructions' => 'Submit a detailed documentation report of your OJT activities, projects, and achievements.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pertinent Documents',
                'description' => 'Other relevant documents from OJT experience',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => json_encode(['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']),
                'max_file_size_mb' => 10,
                'max_files_per_submission' => 1,
                'instructions' => 'Submit any other relevant documents from your OJT experience.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Reset overrides to previous defaults
        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->where('name', 'Weekly Accomplishment Report')
            ->update(['max_files_per_submission' => 4]);

        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->where('name', 'Photo Documentation')
            ->update(['max_files_per_submission' => 50]);

        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->where('name', 'Other Documents Not Specified')
            ->update(['max_files_per_submission' => null]);

        DB::table('document_requirements')
            ->where('type', 'post_placement')
            ->where('name', 'Authenticated Copy of DTR')
            ->update(['max_files_per_submission' => 1]);
    }
};
