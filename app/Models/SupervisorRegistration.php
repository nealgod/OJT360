<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Check if the registration token is expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the registration is verified
     */
    public function isVerified()
    {
        return ! is_null($this->verified_at);
    }

    /**
     * Mark as verified
     */
    public function markAsVerified()
    {
        $this->update(['verified_at' => now()]);
    }

    /**
     * Generate a unique token
     */
    public static function generateToken()
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (self::where('token', $token)->exists());

        return $token;
    }
}
