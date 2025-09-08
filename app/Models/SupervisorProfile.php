<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'employee_id',
        'position',
        'phone',
        'profile_image',
        'evaluation_permissions',
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

    // Relationship with Company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relationship with Students (through company)
    public function students()
    {
        return $this->hasMany(StudentProfile::class, 'assigned_company_id', 'company_id');
    }
}
