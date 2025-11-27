<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'supervisor_user_id',
        'coordinator_user_id',
        'evaluation_month',
        'evaluation_year',
        'month_number',
        'student_name',
        'hte_name',
        'hte_address',
        'work_assignment',
        'work_schedule',
        'supervisor_name',
        'rating_row_1', 'rating_row_2', 'rating_row_3', 'rating_row_4', 'rating_row_5',
        'rating_row_6', 'rating_row_7', 'rating_row_8', 'rating_row_9', 'rating_row_10',
        'rating_row_11', 'rating_row_12', 'rating_row_13', 'rating_row_14', 'rating_row_15',
        'rating_row_16', 'rating_row_17', 'rating_row_18', 'rating_row_19', 'rating_row_20',
        'comments_recommendations',
        'status',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
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
    public function getMonthName(): string
    {
        return Carbon::create()->month($this->evaluation_month)->format('F');
    }

    public function getMonthYearLabel(): string
    {
        return $this->getMonthName().' '.$this->evaluation_year;
    }

    /**
     * Get formatted work schedule for display
     * Try to get from acceptance letter first, otherwise format stored value
     */
    public function getFormattedWorkScheduleAttribute(): string
    {
        // Try to get formatted schedule from acceptance letter
        $acceptance = AcceptanceLetter::where('student_user_id', $this->student_user_id)
            ->latest('start_date')
            ->first();

        if ($acceptance && $acceptance->formatted_work_schedule) {
            return $acceptance->formatted_work_schedule;
        }

        // If stored value is already formatted (contains days and times), return it
        if ($this->work_schedule && (strpos($this->work_schedule, 'Mon') !== false || strpos($this->work_schedule, 'Tue') !== false)) {
            return $this->work_schedule;
        }

        // Otherwise return stored value or N/A
        return $this->work_schedule ?? 'N/A';
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeSubmitted(): bool
    {
        // Check if all 20 ratings are filled
        for ($i = 1; $i <= 20; $i++) {
            if (empty($this->{"rating_row_$i"})) {
                return false;
            }
        }

        return $this->status === 'draft';
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

    // Attribute names for display
    public static function getAttributeNames(): array
    {
        return [
            1 => 'Analytical Skills',
            2 => 'Communicative Competence',
            3 => 'Leadership Skills',
            4 => 'Organizational and Time Management Skills',
            5 => 'Technical Competence',
            6 => 'Accuracy and Dependability',
            7 => 'Creativity',
            8 => 'Multi-Tasking Ability',
            9 => 'Productivity/Work Speed',
            10 => 'Professionalism',
            11 => 'Adaptability to Change',
            12 => 'Attendance and Punctuality',
            13 => 'Courtesy and Respect towards Superiors & Clients',
            14 => 'Professional Grooming and Appearance',
            15 => 'Teamwork/Collaborative Qualities',
            16 => 'Adherence to HTE Policies and Standards',
            17 => 'Attitude towards Work',
            18 => 'Capacity to Work with Colleagues',
            19 => 'Initiative',
            20 => 'Participation in HTE Initiated Activities',
        ];
    }

    public static function getCategoryNames(): array
    {
        return [
            'skills' => 'RELATED SKILLS AND COMPETENCIES',
            'quality' => 'QUALITY OF WORK',
            'approach' => 'WORK APPROACH',
            'cooperation' => 'JOB INTEREST AND COOPERATION',
        ];
    }

    public static function getAttributesByCategory(): array
    {
        return [
            'skills' => [1, 2, 3, 4, 5],
            'quality' => [6, 7, 8, 9, 10],
            'approach' => [11, 12, 13, 14, 15],
            'cooperation' => [16, 17, 18, 19, 20],
        ];
    }

    // Calculation Methods
    public function getCategoryAverage(string $categoryKey): ?float
    {
        $attributes = self::getAttributesByCategory()[$categoryKey] ?? [];
        
        if (empty($attributes)) {
            return null;
        }

        $sum = 0;
        $count = 0;

        foreach ($attributes as $rowNumber) {
            $rating = $this->{"rating_row_$rowNumber"};
            if ($rating !== null) {
                $sum += $rating;
                $count++;
            }
        }

        if ($count === 0) {
            return null;
        }

        return round($sum / $count, 2);
    }

    public function getTotalAverage(): ?float
    {
        $sum = 0;
        $count = 0;

        for ($i = 1; $i <= 20; $i++) {
            $rating = $this->{"rating_row_$i"};
            if ($rating !== null) {
                $sum += $rating;
                $count++;
            }
        }

        if ($count === 0) {
            return null;
        }

        return round($sum / $count, 2);
    }

    // Accessors for easy access in views
    public function getCategoryAveragesAttribute(): array
    {
        return [
            'skills' => $this->getCategoryAverage('skills'),
            'quality' => $this->getCategoryAverage('quality'),
            'approach' => $this->getCategoryAverage('approach'),
            'cooperation' => $this->getCategoryAverage('cooperation'),
        ];
    }

    public function getTotalAverageAttribute(): ?float
    {
        return $this->getTotalAverage();
    }
}
