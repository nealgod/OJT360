<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'company_id',
        'work_date',
        'break_minutes',
        'minutes_worked',
        'overtime_minutes',
        'status',
        'is_recovered',
        'recovery_reason',
        'recovery_approved',
        'recovery_approved_at',
        'recovery_approved_by',
        // Quad-Logging Fields
        'am_in_time', 'am_out_time', 'pm_in_time', 'pm_out_time',
        'am_in_lat', 'am_in_lng', 'am_out_lat', 'am_out_lng',
        'pm_in_lat', 'pm_in_lng', 'pm_out_lat', 'pm_out_lng',
        'am_in_photo', 'am_out_photo', 'pm_in_photo', 'pm_out_photo',
    ];

    protected $casts = [
        'work_date' => 'date',
        'am_in_time' => 'string',
        'am_out_time' => 'string',
        'pm_in_time' => 'string',
        'pm_out_time' => 'string',
        'is_recovered' => 'boolean',
        'recovery_approved' => 'boolean',
        'recovery_approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    // Alias for student relationship (for consistency)
    public function user()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function recoveryApprover()
    {
        return $this->belongsTo(User::class, 'recovery_approved_by');
    }

    protected $appends = ['time_in_formatted', 'time_out_formatted', 'hours_worked_formatted', 'is_complete'];

    // Helper methods for time formatting
    public function getTimeInFormattedAttribute()
    {
        $timeIn = $this->am_in_time ?? $this->pm_in_time;
        if (! $timeIn) {
            return '—';
        }
        try {
            $time = \Carbon\Carbon::parse($timeIn);
            return $time->format('g:i A');
        } catch (\Exception $e) {
            return $timeIn;
        }
    }

    public function getTimeOutFormattedAttribute()
    {
        $timeOut = $this->pm_out_time ?? $this->am_out_time;
        if (! $timeOut) {
            return '—';
        }
        try {
            $time = \Carbon\Carbon::parse($timeOut);
            return $time->format('g:i A');
        } catch (\Exception $e) {
            return $timeOut;
        }
    }

    // Helper method to get formatted hours worked
    public function getHoursWorkedFormattedAttribute()
    {
        if (! $this->minutes_worked) {
            return '0.00';
        }

        return number_format($this->minutes_worked / 60, 2);
    }

    /**
     * Determine if the attendance log is fully completed.
     * A log is complete if:
     * 1. If AM was started, it must be ended.
     * 2. If PM was started, it must be ended.
     * 3. At least one shift was actually started.
     */
    public function getIsCompleteAttribute()
    {
        // 1. If morning started but didn't end
        if ($this->am_in_time && !$this->am_out_time) return false;

        // 2. If afternoon started but didn't end
        if ($this->pm_in_time && !$this->pm_out_time) return false;

        // 3. Must have at least one punch in
        if (!$this->am_in_time && !$this->pm_in_time) return false;

        return true;
    }
}
