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
        'photo_in_path',
        'photo_out_path',
        'minutes_worked',
        'status',
    ];

    protected $casts = [
        'work_date' => 'date',
        'time_in' => 'string',
        'time_out' => 'string',
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
            return \Carbon\Carbon::createFromFormat('H:i:s', $this->time_in)->format('g:i A');
        } catch (\Exception $e) {
            // Fallback if time format is different
            return $this->time_in;
        }
    }

    public function getTimeOutFormattedAttribute()
    {
        if (!$this->time_out) return '—';
        try {
            return \Carbon\Carbon::createFromFormat('H:i:s', $this->time_out)->format('g:i A');
        } catch (\Exception $e) {
            // Fallback if time format is different
            return $this->time_out;
        }
    }
}


