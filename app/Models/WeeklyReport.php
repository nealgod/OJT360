<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'coordinator_user_id',
        'week_start_date',
        'week_end_date',
        'week_number',
        'days_present',
        'days_absent',
        'days_late',
        'total_hours',
        'entries',
        'problems_encountered',
        'supervisor_feedback',
        'supervisor_rating',
        'supervisor_reviewed_at',
        'coordinator_feedback',
        'coordinator_reviewed_at',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'week_end_date' => 'date',
        'submitted_at' => 'datetime',
        'supervisor_reviewed_at' => 'datetime',
        'coordinator_reviewed_at' => 'datetime',
        'entries' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_user_id');
    }

    // Get supervisor through student's profile
    public function getSupervisorAttribute()
    {
        return $this->student?->studentProfile?->supervisor;
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft'
            && ! empty($this->entries)
            && count($this->entries) > 0;
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_user_id', $studentId);
    }

    public function getWeekLabelAttribute(): string
    {
        $start = $this->week_start_date ? $this->week_start_date->format('M d') : '';
        $end = $this->week_end_date ? $this->week_end_date->format('M d, Y') : '';

        return trim("{$start} - {$end}");
    }

    public function getEntriesForDisplayAttribute(): array
    {
        $entries = $this->entries ?? [];

        return collect(range(0, 7))
            ->map(function ($index) use ($entries) {
                $entry = $entries[$index] ?? [];
                $date = isset($entry['date']) && $entry['date']
                    ? Carbon::parse($entry['date'])->format('M d, Y')
                    : '';

                return [
                    'date' => $date,
                    'activity' => $entry['activity'] ?? '',
                    'hours' => $entry['hours'] ?? '',
                ];
            })
            ->all();
    }
}
