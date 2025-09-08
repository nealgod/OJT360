<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoordinatorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'department',
        'department_id',
        'program_id',
        'phone',
        'profile_image',
        'managed_departments',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Relationship with Students (through department)
    public function students()
    {
        return $this->hasManyThrough(
            StudentProfile::class,
            User::class,
            'id', // Foreign key on users table
            'user_id', // Foreign key on student_profiles table
            'user_id', // Local key on coordinator_profiles table
            'id' // Local key on users table
        )->where('users.role', 'intern');
    }
}
