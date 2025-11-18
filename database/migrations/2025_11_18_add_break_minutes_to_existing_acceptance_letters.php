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
        // Update existing acceptance letters to include break_minutes in work_schedule
        $letters = DB::table('acceptance_letters')->get();
        
        foreach ($letters as $letter) {
            $workSchedule = json_decode($letter->work_schedule, true);
            
            // Only update if break_minutes doesn't exist
            if (!isset($workSchedule['break_minutes'])) {
                // Default to 60 minutes (1 hour) break time
                $workSchedule['break_minutes'] = 60;
                
                DB::table('acceptance_letters')
                    ->where('id', $letter->id)
                    ->update(['work_schedule' => json_encode($workSchedule)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove break_minutes from work_schedule
        $letters = DB::table('acceptance_letters')->get();
        
        foreach ($letters as $letter) {
            $workSchedule = json_decode($letter->work_schedule, true);
            
            if (isset($workSchedule['break_minutes'])) {
                unset($workSchedule['break_minutes']);
                
                DB::table('acceptance_letters')
                    ->where('id', $letter->id)
                    ->update(['work_schedule' => json_encode($workSchedule)]);
            }
        }
    }
};
