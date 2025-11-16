<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AcceptanceRequestController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        
        // Check if student has submitted resume and application (active submissions only)
        // Only check for pre-placement application letter
        // Cancelled submissions are deleted, so they won't be found
        $hasResume = \App\Models\Resume::where('user_id', $user->id)->exists();
        $hasApplication = \App\Models\StudentDocumentSubmission::where('student_user_id', $user->id)
            ->whereHas('requirement', function($q) {
                $q->where('name', 'LIKE', '%Application Letter%')
                  ->where('type', 'pre_placement');
            })
            ->whereIn('status', ['submitted', 'approved', 'rejected']) // Only active submissions
            ->exists();
        
        if (!$hasResume || !$hasApplication) {
            return redirect()->route('documents.index')
                ->withErrors(['error' => 'You must submit your Resume and Application Letter before requesting an acceptance letter.']);
        }
        
        // Check if there's already a pending request
        $pendingRequest = AcceptanceRequest::where('student_user_id', $user->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();
        
        return view('acceptance.request', compact('pendingRequest'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check for existing pending request
        $existingRequest = AcceptanceRequest::where('student_user_id', $user->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();
        
        if ($existingRequest) {
            return back()->withErrors(['error' => 'You already have a pending acceptance letter request. Please wait for the supervisor to respond or cancel the existing request.']);
        }
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'supervisor_name' => 'required|string|max:255',
            'supervisor_email' => 'required|email|max:255',
            'position' => 'required|string|max:255',
        ]);

        // Create acceptance request
        $acceptanceRequest = AcceptanceRequest::create([
            'student_user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'supervisor_name' => $validated['supervisor_name'],
            'supervisor_email' => $validated['supervisor_email'],
            'position' => $validated['position'],
            'token' => Str::random(64),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // Send email to supervisor
        try {
            \Mail::to($validated['supervisor_email'])->send(new \App\Mail\SupervisorAcceptanceInvitation($acceptanceRequest));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send supervisor invitation email: ' . $e->getMessage());
        }

        // Create notification for supervisor if they have an account
        $supervisor = \App\Models\User::where('email', $validated['supervisor_email'])
            ->where('role', 'supervisor')
            ->first();
        
        if ($supervisor) {
            \App\Models\Notification::create([
                'user_id' => $supervisor->id,
                'type' => 'acceptance_letter_request',
                'title' => '📝 New Acceptance Letter Request',
                'message' => $user->name . ' has requested an acceptance letter for ' . $validated['position'] . ' at ' . $validated['company_name'] . '.',
                'data' => [
                    'request_id' => $acceptanceRequest->id,
                    'student_id' => $user->id,
                    'student_name' => $user->name,
                    'company' => $validated['company_name'],
                    'position' => $validated['position'],
                    'token' => $acceptanceRequest->token,
                    'action_url' => route('supervisor.acceptance.create', $acceptanceRequest->token),
                    'action_text' => 'Generate Letter',
                ],
                'read' => false,
            ]);
        }

        return redirect()->route('documents.index')
            ->with('success', 'Acceptance letter request sent to ' . $validated['supervisor_name'] . '! They will receive an email with instructions.');
    }

    public function cancel(AcceptanceRequest $request)
    {
        $user = Auth::user();
        
        if ($request->student_user_id !== $user->id) {
            abort(403);
        }
        
        if ($request->status !== 'pending') {
            return back()->withErrors(['error' => 'Cannot cancel a request that has already been processed.']);
        }

        $request->update(['status' => 'cancelled']);

        return back()->with('success', 'Acceptance letter request cancelled successfully.');
    }
}
