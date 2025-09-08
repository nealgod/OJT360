<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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
        
        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Update basic user information
        $user->fill($request->only(['name', 'email']));

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
        $profileData = $request->only(['student_id', 'course', 'department', 'phone']);
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $profileData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        if ($user->studentProfile) {
            $user->studentProfile->update($profileData);
        } else {
            $user->studentProfile()->create($profileData);
        }
    }

    /**
     * Update coordinator profile information.
     */
    private function updateCoordinatorProfile(ProfileUpdateRequest $request, $user)
    {
        $profileData = $request->only(['employee_id', 'phone']);
        
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
        $profileData = $request->only(['employee_id', 'position', 'phone']);
        
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
