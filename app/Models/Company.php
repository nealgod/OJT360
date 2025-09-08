<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'coordinator_id',
        'address',
        'contact_person',
        'contact_email',
        'contact_phone',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationship with StudentProfiles
    public function students()
    {
        return $this->hasMany(StudentProfile::class, 'assigned_company_id');
    }

    // Relationship with SupervisorProfiles
    public function supervisors()
    {
        return $this->hasMany(SupervisorProfile::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function placementRequests()
    {
        return $this->hasMany(PlacementRequest::class);
    }
}
