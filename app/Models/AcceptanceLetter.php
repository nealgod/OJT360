<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptanceLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'acceptance_request_id',
        'student_user_id',
        'supervisor_user_id',
        'company_id',
        'job_title',
        'department',
        'immediate_supervisor',
        'start_date',
        'end_date',
        'total_hours',
        'work_schedule',
        'signature_type',
        'signature_data',
        'additional_notes',
        'letter_path',
        'document_id',
        'generated_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'work_schedule' => 'array',
        'generated_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Helper methods
    public function getFormattedDateRangeAttribute()
    {
        return $this->start_date->format('F d, Y') . ' - ' . $this->end_date->format('F d, Y');
    }

    public function getWorkScheduleTextAttribute()
    {
        if (!$this->work_schedule) {
            return '';
        }

        $schedule = is_string($this->work_schedule) ? json_decode($this->work_schedule, true) : $this->work_schedule;
        
        if (!is_array($schedule)) {
            return '';
        }

        $days = [];
        foreach ($schedule as $day => $hours) {
            if (!$hours || $hours === 'Off') {
                continue;
            }
            
            // Handle if $hours is an array with start_time, end_time
            if (is_array($hours)) {
                if (isset($hours['start_time']) && isset($hours['end_time'])) {
                    $timeStr = $hours['start_time'] . '-' . $hours['end_time'];
                    $days[] = ucfirst($day) . ': ' . $timeStr;
                }
            } else {
                // Handle if $hours is a string like "8:00-17:00"
                $days[] = ucfirst($day) . ': ' . $hours;
            }
        }

        return implode(', ', $days);
    }

    /**
     * Format work schedule for display (Mon, Tue, Wed (8:00 AM to 5:00 PM))
     */
    public function getFormattedWorkScheduleAttribute(): string
    {
        if (!$this->work_schedule) {
            return 'N/A';
        }

        $schedule = is_string($this->work_schedule) 
            ? json_decode($this->work_schedule, true) 
            : $this->work_schedule;

        if (!is_array($schedule)) {
            return 'N/A';
        }

        // Map full day names to 3-letter abbreviations
        $dayAbbreviations = [
            'monday' => 'Mon',
            'tuesday' => 'Tue',
            'wednesday' => 'Wed',
            'thursday' => 'Thu',
            'friday' => 'Fri',
            'saturday' => 'Sat',
            'sunday' => 'Sun'
        ];

        // Extract working days
        $workingDays = [];
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            if (isset($schedule[$day]['enabled']) && $schedule[$day]['enabled']) {
                $workingDays[] = $dayAbbreviations[$day] ?? ucfirst(substr($day, 0, 3));
            }
        }

        if (empty($workingDays)) {
            return 'N/A';
        }

        // Get shift times
        $shiftStart = $schedule['shift_start'] ?? '08:00';
        $shiftEnd = $schedule['shift_end'] ?? '17:00';

        // Format time to 12-hour format
        $formatTime = function($time) {
            try {
                // Try H:i:s format first
                $parsed = \Carbon\Carbon::createFromFormat('H:i:s', $time);
            } catch (\Exception $e) {
                try {
                    // Try H:i format
                    $parsed = \Carbon\Carbon::createFromFormat('H:i', $time);
                } catch (\Exception $e) {
                    return $time;
                }
            }
            return $parsed->format('g:i A');
        };

        $startTime = $formatTime($shiftStart);
        $endTime = $formatTime($shiftEnd);

        // Format as: "Mon, Tue, Wed (8:00 AM to 5:00 PM)"
        $daysStr = implode(', ', $workingDays);
        return $daysStr . ' (' . $startTime . ' to ' . $endTime . ')';
    }
}
