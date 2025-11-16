<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'company_name',
        'supervisor_name',
        'supervisor_email',
        'position',
        'token',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function acceptanceLetter()
    {
        return $this->hasOne(AcceptanceLetter::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                     ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
                     ->where('expires_at', '<=', now());
    }

    // Helper methods
    public function isExpired()
    {
        return $this->status === 'pending' && $this->expires_at->isPast();
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
