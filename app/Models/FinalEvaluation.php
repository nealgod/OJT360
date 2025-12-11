<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id', 'supervisor_user_id', 'coordinator_user_id',
        'control_number', 'revision_number',
        'student_name', 'student_id', 'course', 'department',
        'hte_name', 'hte_address', 'internship_start_date', 'internship_end_date',
        'total_hours_rendered',
        'rating_quality_thoroughness', 'rating_dependability',
        'rating_quality_completion', 'rating_attendance',
        'rating_cooperation', 'rating_judgement', 'rating_personality',
        'total_rating', 'comments_recommendations',
        'supervisor_name', 'supervisor_signature_date',
        'student_confirmed', 'student_signature_date',
        'status', 'submitted_at', 'reviewed_at',
    ];

    protected $casts = [
        'internship_start_date' => 'date',
        'internship_end_date' => 'date',
        'supervisor_signature_date' => 'date',
        'student_signature_date' => 'date',
        'student_confirmed' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_user_id');
    }

    // Helper Methods
    public static function generateControlNumber(string $studentId): string
    {
        $year = now()->year;
        // Format: FE-STUDENTID-YEAR (e.g., FE-2021001-2024)
        return sprintf('FE-%s-%d', $studentId, $year);
    }

    public function calculateTotalRating()
    {
        $sum = ($this->rating_quality_thoroughness ?? 0) +
               ($this->rating_dependability ?? 0) +
               ($this->rating_quality_completion ?? 0) +
               ($this->rating_attendance ?? 0) +
               ($this->rating_cooperation ?? 0) +
               ($this->rating_judgement ?? 0) +
               ($this->rating_personality ?? 0);
               
        // If whole number, format without decimals, otherwise max 2 decimals
        return fmod($sum, 1) == 0 ? number_format($sum, 0) : number_format($sum, 2);
    }

    public function canBeSubmitted(): bool
    {
        return ! empty($this->rating_quality_thoroughness) &&
               ! empty($this->rating_dependability) &&
               ! empty($this->rating_quality_completion) &&
               ! empty($this->rating_attendance) &&
               ! empty($this->rating_cooperation) &&
               ! empty($this->rating_judgement) &&
               ! empty($this->rating_personality);
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    public static function getMaxRatings(): array
    {
        return [
            'rating_quality_thoroughness' => 20,
            'rating_dependability' => 15,
            'rating_quality_completion' => 20,
            'rating_attendance' => 15,
            'rating_cooperation' => 10,
            'rating_judgement' => 10,
            'rating_personality' => 5,
        ];
    }

    public static function getCriteriaLabels(): array
    {
        return [
            'rating_quality_thoroughness' => 'Quality of work',
            'rating_dependability' => 'Dependability, Reliability, and Resourcefulness',
            'rating_quality_completion' => 'Quality of work',
            'rating_attendance' => 'Attendance',
            'rating_cooperation' => 'Cooperation',
            'rating_judgement' => 'Judgement',
            'rating_personality' => 'Personality',
        ];
    }

    public static function getCriteriaDescriptions(): array
    {
        return [
            'rating_quality_thoroughness' => 'Thoroughness, Accuracy, Neat, & Effectiveness',
            'rating_dependability' => 'Ability to work with maximum amount of supervision',
            'rating_quality_completion' => 'Able to complete work in allotted time',
            'rating_attendance' => 'Regularity and punctuality in attendance and proper observation of break/meet period',
            'rating_cooperation' => 'Works well with everyone, good teamwork',
            'rating_judgement' => 'Sound decisions, ability to identify and evaluate pertinent factor',
            'rating_personality' => 'Personal grooming and pleasant disposition',
        ];
    }

    // Scopes
    public function scopeForSupervisor($query, int $supervisorId)
    {
        return $query->where('supervisor_user_id', $supervisorId);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_user_id', $studentId);
    }

    public function scopeForCoordinator($query, int $coordinatorId)
    {
        return $query->where('coordinator_user_id', $coordinatorId);
    }
}
