<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\VerifyWithTemporaryPassword;
use App\Models\Department;
use App\Models\CoordinatorProfile;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::with(['studentProfile', 'coordinatorProfile', 'supervisorProfile'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        $departments = Department::with('programs:id,department_id,name')
            ->get(['id','name']);

        return view('admin.create-user', compact('departments'));
    }

    public function storeUser(Request $request)
    {
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['required', 'in:coordinator,supervisor'],
        ];

        if ($request->role === 'coordinator') {
            $rules['department_id'] = ['required', 'exists:departments,id'];
            $rules['program_id'] = ['required', 'exists:programs,id'];
        }

        $validated = $request->validate($rules);

        // Coordinator: send invitation link and do not create user yet
        if ($request->role === 'coordinator') {
            if (User::where('email', $request->email)->exists()) {
                return back()->withErrors(['email' => 'Email is already in use by another account.'])->withInput();
            }

            $token = Str::random(64);
            $invite = \App\Models\CoordinatorInvitation::create([
                'email' => strtolower($request->email),
                'token' => $token,
                'department_id' => $request->integer('department_id'),
                'program_id' => $request->integer('program_id'),
                'expires_at' => now()->addHour(),
            ]);

            $link = \Illuminate\Support\Facades\URL::route('coordinator.complete.show', ['token' => $invite->token]);
            try {
                \Illuminate\Support\Facades\Mail::to($invite->email)->send(new \App\Mail\CoordinatorInvitationMail($link));
            } catch (\Exception $e) {
                \Log::error('Coordinator invite email failed: ' . $e->getMessage());
            }

            return redirect()->route('admin.users')->with('success', 'Coordinator invite sent successfully.');
        }

        // Supervisor: keep temp password flow
        $temporaryPassword = Str::random(16);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($temporaryPassword),
            'role' => 'supervisor',
            'must_change_password' => true,
        ]);
        try {
            $user->notify(new VerifyWithTemporaryPassword($temporaryPassword));
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.users')->with('success', 'Supervisor account created and credentials emailed.');
    }
}
