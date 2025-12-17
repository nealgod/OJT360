<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\ProgramCodeResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Load the appropriate profile based on user role
        $profile = $user->getProfile();

        $yearLevels = ProgramCodeResolver::yearLevels();
        $sectionOptions = $user->isStudent()
            ? ProgramCodeResolver::sectionsForCourse($user->studentProfile?->course)
            : [];

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
            'yearLevels' => $yearLevels,
            'sectionOptions' => $sectionOptions,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Update basic user information
        if ($user->isStudent()) {
            // Lock name for students; allow email edits only via admin/coordinator rules
            $user->fill($request->only(['email']));
        } elseif ($user->isSupervisor()) {
            // Supervisors can update name but not email (set during registration)
            $user->fill($request->only(['name']));
        } else {
            $user->fill($request->only(['name', 'email']));
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Handle profile-specific data
        if ($user->isStudent()) {
            $this->updateStudentProfile($request, $user);
        } elseif ($user->isCoordinator()) {
            $this->updateCoordinatorProfile($request, $user);
        } elseif ($user->isSupervisor()) {
            $this->updateSupervisorProfile($request, $user);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update student profile information.
     */
    private function updateStudentProfile(ProfileUpdateRequest $request, $user)
    {
        $profileData = $request->only([
            'student_id',
            'course',
            'department',
            'phone',
            'address',
            'year_level',
            'section',
        ]);

        if (isset($profileData['section'])) {
            $profileData['section'] = strtoupper($profileData['section']);
        }

        $profileData['course_section_code'] = ProgramCodeResolver::buildCourseSectionCode(
            $profileData['course'] ?? $user->studentProfile?->course,
            $profileData['year_level'] ?? $user->studentProfile?->year_level,
            $profileData['section'] ?? $user->studentProfile?->section
        );

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $profileData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        $studentProfile = $user->studentProfile;

        if ($studentProfile) {
            $studentProfile->update($profileData);
        } else {
            $studentProfile = $user->studentProfile()->create($profileData);
        }

        if ($studentProfile && is_null($studentProfile->required_hours)) {
            $studentProfile->update([
                'required_hours' => $user->getRequiredHours(),
            ]);
        }
    }

    /**
     * Update coordinator profile information.
     */
    private function updateCoordinatorProfile(ProfileUpdateRequest $request, $user)
    {
        $profileData = $request->only(['phone']);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $profileData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        if ($user->coordinatorProfile) {
            $user->coordinatorProfile->update($profileData);
        } else {
            $user->coordinatorProfile()->create($profileData);
        }
    }

    /**
     * Update supervisor profile information.
     */
    private function updateSupervisorProfile(ProfileUpdateRequest $request, $user)
    {
        $profileData = $request->only(['position', 'phone']);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $profileData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        if ($user->supervisorProfile) {
            $user->supervisorProfile->update($profileData);
        } else {
            // For supervisors, we need a company_id - this should be set when creating the supervisor account
            $profileData['company_id'] = $user->supervisorProfile->company_id ?? 1; // Default company
            $user->supervisorProfile()->create($profileData);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
