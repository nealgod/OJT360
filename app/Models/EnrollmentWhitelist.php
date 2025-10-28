<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentWhitelist extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'contact_number',
        'program_id',
        'email',
        'department_id',
        'status',
        'notes',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}


