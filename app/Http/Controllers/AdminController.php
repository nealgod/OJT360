<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTemporaryCredentials;
use App\Notifications\VerifyWithTemporaryPassword;
use App\Models\Department;
use App\Models\CoordinatorProfile;

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:coordinator,supervisor'],
        ];

        if ($request->role === 'coordinator') {
            $rules['department_id'] = ['required', 'exists:departments,id'];
            $rules['program_id'] = ['required', 'exists:programs,id'];
        }

        $validated = $request->validate($rules);

        // Generate a temporary secure password
        $temporaryPassword = Str::random(16);

        // Create user with unverified email
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($temporaryPassword),
            'role' => $request->role,
            'must_change_password' => true,
        ]);

        // Create role-specific profile
        if ($user->role === 'coordinator') {
            $deptName = optional(Department::find($request->integer('department_id')))->name;
            CoordinatorProfile::create([
                'user_id' => $user->id,
                'department_id' => $request->integer('department_id'),
                'program_id' => $request->integer('program_id'),
                'department' => $deptName,
                'status' => 'active',
            ]);
        }

        // For coordinator/supervisor: send single email with verification link and temporary password
        if (in_array($user->role, ['coordinator', 'supervisor'])) {
            $user->notify(new VerifyWithTemporaryPassword($temporaryPassword));
        } else {
            // Fallback (students/admins): use default verification
            if (method_exists($user, 'sendEmailVerificationNotification')) {
                $user->sendEmailVerificationNotification();
            }
        }

        return redirect()->route('admin.users')->with('success', 'User created. Verification email with credentials sent.');
    }
}
