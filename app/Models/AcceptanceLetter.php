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
    public function acceptanceRequest()
    {
        return $this->belongsTo(AcceptanceRequest::class);
    }

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

        $days = [];
        foreach ($this->work_schedule as $day => $hours) {
            if ($hours && $hours !== 'Off') {
                $days[] = ucfirst($day) . ': ' . $hours;
            }
        }

        return implode(', ', $days);
    }
}
