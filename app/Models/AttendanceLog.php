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
        'break_minutes',
        'photo_in_path',
        'photo_out_path',
        'minutes_worked',
        'status',
        'is_recovered',
        'recovery_reason',
        'recovery_approved',
        'recovery_approved_at',
        'recovery_approved_by',
        'lat_in',
        'lng_in',
        'lat_out',
        'lng_out',
    ];

    protected $casts = [
        'work_date' => 'date',
        'time_in' => 'string',
        'time_out' => 'string',
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

    // Helper methods for time formatting
    public function getTimeInFormattedAttribute()
    {
        if (! $this->time_in) {
            return '—';
        }
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
        if (! $this->time_out) {
            return '—';
        }
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
        if (! $this->minutes_worked) {
            return '0.00';
        }

        return number_format($this->minutes_worked / 60, 2);
    }
}
