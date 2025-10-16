<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'must_change_password' => 'boolean',
    ];

    // Role-specific profile relationships
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function coordinatorProfile()
    {
        return $this->hasOne(CoordinatorProfile::class);
    }

    public function supervisorProfile()
    {
        return $this->hasOne(SupervisorProfile::class);
    }

    // Notifications relationship
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Messaging relationships
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function unreadMessages()
    {
        return $this->receivedMessages()->unread();
    }

    public function placementRequests()
    {
        return $this->hasMany(PlacementRequest::class, 'student_user_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'student_user_id');
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, 'student_user_id');
    }

    public function documentSubmissions()
    {
        return $this->hasMany(StudentDocumentSubmission::class, 'student_user_id');
    }

    // For supervisors: get all students they supervise
    public function studentProfiles()
    {
        return $this->hasMany(StudentProfile::class, 'supervisor_id');
    }

    // Role checking methods
    public function isStudent()
    {
        return $this->role === 'intern';
    }

    public function isCoordinator()
    {
        return $this->role === 'coordinator';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Check if user has active OJT (for students)
    public function hasActiveOJT()
    {
        return $this->studentProfile && $this->studentProfile->ojt_status === 'active';
    }

    // Get the appropriate profile based on role
    public function getProfile()
    {
        return match($this->role) {
            'intern' => $this->studentProfile,
            'coordinator' => $this->coordinatorProfile,
            'supervisor' => $this->supervisorProfile,
            default => null
        };
    }

    // Get required OJT hours for student's course
    public function getRequiredHours() {
        if (!$this->isStudent() || !$this->studentProfile) {
            return 0;
        }

        // If coordinator has set custom hours, use that
        if ($this->studentProfile->required_hours) {
            return $this->studentProfile->required_hours;
        }

        // Otherwise use default from config
        $departments = config('departments.departments');
        $department = $this->studentProfile->department;
        $course = $this->studentProfile->course;

        if (isset($departments[$department]['courses'][$course])) {
            return $departments[$department]['courses'][$course];
        }

        return 0;
    }

    // Get completed hours
    public function getCompletedHours() {
        if (!$this->isStudent() || !$this->studentProfile) {
            return 0;
        }

        return $this->studentProfile->completed_hours ?? 0;
    }

    // Get remaining hours
    public function getRemainingHours() {
        return $this->getRequiredHours() - $this->getCompletedHours();
    }
}
