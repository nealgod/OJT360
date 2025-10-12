<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'company_id',
        'status',
        'start_date',
        'contact_person',
        'supervisor_name',
        'supervisor_email',
        'note',
        'proof_path',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'start_date' => 'date',
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

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
