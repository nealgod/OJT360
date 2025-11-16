<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Set all requirements to max 1 file by default
        DB::table('document_requirements')->update(['max_files_per_submission' => 1]);
        
        // Set specific requirements to allow multiple files (up to 50)
        $multipleFileRequirements = [
            'Photo Documentation',
            'Other Documents Not',
            'Weekly Accomplishment Report',
        ];
        
        foreach ($multipleFileRequirements as $reqName) {
            DB::table('document_requirements')
                ->where('name', 'LIKE', '%' . $reqName . '%')
                ->update(['max_files_per_submission' => 50]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore default to 2
        DB::table('document_requirements')->update(['max_files_per_submission' => 2]);
    }
};
