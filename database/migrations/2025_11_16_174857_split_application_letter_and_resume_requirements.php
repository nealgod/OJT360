<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DocumentRequirement;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Find the existing "Application Letter and PDS/Resume" requirement
        $oldRequirement = DocumentRequirement::where('name', 'LIKE', '%Application Letter%')
            ->where('type', 'pre_placement')
            ->first();
        
        if ($oldRequirement) {
            // Update the existing requirement to be just "Application Letter"
            $oldRequirement->update([
                'name' => 'Application Letter',
                'description' => 'Submit your application letter addressed to the company/organization where you will do your OJT.',
                'max_files_per_submission' => 1,
                'instructions' => 'Your application letter should be professionally written and addressed to the company where you will conduct your OJT.',
            ]);
            
            // Create new "PDS/Resume" requirement
            DocumentRequirement::create([
                'name' => 'Personal Data Sheet (PDS) / Resume',
                'description' => 'Submit your Personal Data Sheet (PDS) or Resume with complete personal and educational information.',
                'type' => 'pre_placement',
                'file_types' => ['pdf', 'doc', 'docx'], // Will be cast to JSON automatically
                'max_file_size_mb' => 5, // 5MB
                'max_files_per_submission' => 1,
                'is_required' => true,
                'instructions' => 'Your PDS/Resume should include your complete personal information, educational background, skills, and any relevant experience.',
                'is_active' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Find and delete the PDS/Resume requirement
        $pdsRequirement = DocumentRequirement::where('name', 'LIKE', '%Personal Data Sheet%')
            ->where('type', 'pre_placement')
            ->first();
        
        if ($pdsRequirement) {
            $pdsRequirement->delete();
        }
        
        // Restore the original combined requirement
        $appLetterRequirement = DocumentRequirement::where('name', 'Application Letter')
            ->where('type', 'pre_placement')
            ->first();
        
        if ($appLetterRequirement) {
            $appLetterRequirement->update([
                'name' => 'Application Letter and PDS/Resume',
                'description' => 'Submit both your application letter and Personal Data Sheet (PDS) or Resume.',
                'max_files_per_submission' => 2,
                'instructions' => 'Upload both files: (1) Application Letter and (2) PDS/Resume. Use the "Add Files" button to select both files.',
            ]);
        }
    }
};
