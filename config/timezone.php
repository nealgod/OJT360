<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | The default timezone for all attendance operations.
    | This ensures consistency across all users regardless of their device timezone.
    |
    */
    'default' => 'Asia/Manila',
    
    /*
    |--------------------------------------------------------------------------
    | Timezone Validation
    |--------------------------------------------------------------------------
    |
    | Maximum work duration in minutes (16 hours)
    | Maximum break duration in minutes (4 hours)
    |
    */
    'max_work_duration' => 960, // 16 hours
    'max_break_duration' => 240, // 4 hours
    'default_break_duration' => 60, // 1 hour
];
