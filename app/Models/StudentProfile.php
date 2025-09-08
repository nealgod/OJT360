<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'course',
        'department',
        'phone',
        'profile_image',
        'ojt_status',
        'required_hours',
        'completed_hours',
        'assigned_company_id',
    ];

    protected $casts = [
        'ojt_status' => 'string',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Company
    public function company()
    {
        return $this->belongsTo(Company::class, 'assigned_company_id');
    }

    // Relationship with Coordinator (through department)
    public function coordinator()
    {
        return $this->hasOneThrough(
            CoordinatorProfile::class,
            User::class,
            'id', // Foreign key on users table
            'user_id', // Foreign key on coordinator_profiles table
            'user_id', // Local key on student_profiles table
            'id' // Local key on users table
        )->where('users.role', 'coordinator');
    }
}
