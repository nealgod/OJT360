<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'work_date',
        'summary',
        'attachment_path',
        'status',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}


