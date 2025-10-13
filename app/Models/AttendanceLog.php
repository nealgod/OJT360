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
        'time_in',
        'time_out',
        'break_start',
        'break_end',
        'break_minutes',
        'photo_in_path',
        'photo_out_path',
        'minutes_worked',
        'overtime_minutes',
        'regular_minutes',
        'status',
        'company_schedule',
    ];

    protected $casts = [
        'work_date' => 'date',
        'time_in' => 'string',
        'time_out' => 'string',
        'break_start' => 'string',
        'break_end' => 'string',
        'company_schedule' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    // Helper methods for time formatting
    public function getTimeInFormattedAttribute()
    {
        if (!$this->time_in) return '—';
        try {
            // Create a Carbon instance from the time string and format it properly
            $time = \Carbon\Carbon::createFromFormat('H:i:s', $this->time_in);
            return $time->format('g:i A');
        } catch (\Exception $e) {
            // Fallback if time format is different
            return $this->time_in;
        }
    }

    public function getTimeOutFormattedAttribute()
    {
        if (!$this->time_out) return '—';
        try {
            // Create a Carbon instance from the time string and format it properly
            $time = \Carbon\Carbon::createFromFormat('H:i:s', $this->time_out);
            return $time->format('g:i A');
        } catch (\Exception $e) {
            // Fallback if time format is different
            return $this->time_out;
        }
    }

    // Helper method to get formatted hours worked
    public function getHoursWorkedFormattedAttribute()
    {
        if (!$this->minutes_worked) return '0.00';
        return number_format($this->minutes_worked / 60, 2);
    }

    // Get regular hours worked (excluding overtime)
    public function getRegularHoursFormattedAttribute()
    {
        if (!$this->regular_minutes) return '0.00';
        return number_format($this->regular_minutes / 60, 2);
    }

    // Get overtime hours worked
    public function getOvertimeHoursFormattedAttribute()
    {
        if (!$this->overtime_minutes) return '0.00';
        return number_format($this->overtime_minutes / 60, 2);
    }

    // Get break time formatted
    public function getBreakTimeFormattedAttribute()
    {
        if (!$this->break_minutes) return '0.00';
        return number_format($this->break_minutes / 60, 2);
    }

    // Check if student worked overtime
    public function hasOvertimeAttribute()
    {
        return $this->overtime_minutes > 0;
    }

    // Get total productive hours (excluding breaks)
    public function getProductiveHoursAttribute()
    {
        $totalMinutes = $this->minutes_worked;
        $breakMinutes = $this->break_minutes ?? 0;
        $productiveMinutes = max(0, $totalMinutes - $breakMinutes);
        return $productiveMinutes / 60;
    }
}


