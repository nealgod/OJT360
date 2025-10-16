<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorAssignmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'company_id',
        'proposed_name',
        'proposed_email',
        'notes',
        'status',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}



