<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];

        // Add student-specific validation rules
        if ($this->user()->isStudent()) {
            $rules = array_merge($rules, [
                'student_id' => ['required', 'string', 'max:255', 'unique:student_profiles,student_id,' . ($this->user()->studentProfile->id ?? 'NULL')],
                'course' => ['required', 'string', 'max:255'],
                'department' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);
        }

        // Add coordinator-specific validation rules
        if ($this->user()->isCoordinator()) {
            $rules = array_merge($rules, [
                'employee_id' => ['required', 'string', 'max:255', 'unique:coordinator_profiles,employee_id,' . ($this->user()->coordinatorProfile->id ?? 'NULL')],
                // Department and program are fixed by admin; display-only
                'phone' => ['nullable', 'string', 'max:20'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);
        }

        // Add supervisor-specific validation rules
        if ($this->user()->isSupervisor()) {
            $rules = array_merge($rules, [
                'employee_id' => ['nullable', 'string', 'max:255'],
                'position' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);
        }

        return $rules;
    }
}
