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
        'email_verified_at',
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

    /**
     * Count unique senders with unread messages
     * Used for navigation badge
     */
    public function unreadConversationsCount()
    {
        return $this->unreadMessages()->distinct('sender_id')->count('sender_id');
    }

    // Removed placementRequests - using acceptance letters now

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'student_user_id');
    }

    public function weeklyReports()
    {
        return $this->hasMany(WeeklyReport::class, 'student_user_id');
    }

    public function monthlyEvaluations()
    {
        return $this->hasMany(MonthlyEvaluation::class, 'student_user_id');
    }

    public function finalEvaluation()
    {
        return $this->hasOne(FinalEvaluation::class, 'student_user_id');
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

    public function acceptanceLetters()
    {
        return $this->hasMany(AcceptanceLetter::class, 'student_user_id');
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
        if (! $this->isStudent() || ! $this->studentProfile) {
            return false;
        }

        // Completed students should still have access to the system (just not attendance logging)
        return $this->studentProfile->preplacement_complete 
            || $this->studentProfile->ojt_status === 'active' 
            || $this->studentProfile->ojt_status === 'completed';
    }

    // Check if user completed their profile (aligned with CheckProfileCompletion)
    public function hasCompletedProfile(): bool
    {
        if ($this->isStudent()) {
            $profile = $this->studentProfile;

            return (bool) ($profile && $profile->student_id && $profile->course && $profile->department);
        }
        if ($this->isCoordinator()) {
            $profile = $this->coordinatorProfile;

            return (bool) ($profile && $profile->employee_id && $profile->department);
        }
        if ($this->isSupervisor()) {
            $profile = $this->supervisorProfile;

            return (bool) ($profile && $profile->company_id);
        }

        return true; // Admins considered complete
    }

    // Get the appropriate profile based on role
    public function getProfile()
    {
        return match ($this->role) {
            'intern' => $this->studentProfile,
            'coordinator' => $this->coordinatorProfile,
            'supervisor' => $this->supervisorProfile,
            default => null
        };
    }

    // Get required OJT hours for student's course
    public function getRequiredHours()
    {
        if (! $this->isStudent() || ! $this->studentProfile) {
            return 0;
        }

        // 1. If coordinator has set custom hours for this student, use that
        if ($this->studentProfile->required_hours) {
            return $this->studentProfile->required_hours;
        }

        // 2. Check if linked program has required hours (set by coordinator)
        if ($this->studentProfile->program && $this->studentProfile->program->required_hours) {
            return $this->studentProfile->program->required_hours;
        }

        // 3. Fallback: look up program by name
        $program = \App\Models\Program::where('name', $this->studentProfile->course)->first();
        if ($program && $program->required_hours) {
            return $program->required_hours;
        }

        // 4. Otherwise use default from config
        $departments = config('departments.departments');
        $department = $this->studentProfile->department;
        $course = $this->studentProfile->course;

        if (isset($departments[$department]['courses'][$course])) {
            return $departments[$department]['courses'][$course];
        }

        return 0;
    }

    // Get completed hours
    public function getCompletedHours()
    {
        if (! $this->isStudent() || ! $this->studentProfile) {
            return 0;
        }

        return $this->studentProfile->completed_hours ?? 0;
    }

    // Get remaining hours
    public function getRemainingHours()
    {
        return $this->getRequiredHours() - $this->getCompletedHours();
    }

    /**
     * Get user's profile image URL from their profile settings
     */
    public function getProfileImageAttribute()
    {
        // For students, check StudentProfile
        if ($this->isStudent() && $this->studentProfile && $this->studentProfile->profile_image) {
            return \Illuminate\Support\Facades\Storage::url($this->studentProfile->profile_image);
        }

        // For coordinators, check CoordinatorProfile
        if ($this->isCoordinator() && $this->coordinatorProfile && $this->coordinatorProfile->profile_image) {
            return \Illuminate\Support\Facades\Storage::url($this->coordinatorProfile->profile_image);
        }

        // For supervisors, check SupervisorProfile
        if ($this->isSupervisor() && $this->supervisorProfile && $this->supervisorProfile->profile_image) {
            return \Illuminate\Support\Facades\Storage::url($this->supervisorProfile->profile_image);
        }

        return null;
    }

    /**
     * Get the avatar color class based on user's initial
     */
    public function getAvatarColor()
    {
        $initials = strtoupper(substr($this->name, 0, 1));
        $colors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
        $colorIndex = ord($initials) % count($colors);

        return $colors[$colorIndex];
    }

    /**
     * Get user's avatar (profile image or initials)
     */
    public function getAvatarHtml($size = 'w-10 h-10')
    {
        $profileImage = $this->profile_image;
        $initials = strtoupper(substr($this->name, 0, 1));
        $avatarColor = $this->getAvatarColor();

        if ($profileImage) {
            return '<img src="'.$profileImage.'" alt="'.$this->name.'" class="'.$size.' rounded-full object-cover">';
        } else {
            return '<div class="'.$size.' '.$avatarColor.' rounded-full flex items-center justify-center text-white font-bold">'.$initials.'</div>';
        }
    }
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\QueuedResetPassword($token));
    }
}
