<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class MessageController extends Controller
{
    /**
     * Display messages for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            // Students see messages they sent and received
            $messages = Message::where('sender_id', $user->id)
                ->orWhere('recipient_id', $user->id)
                ->with(['sender', 'recipient'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->isCoordinator()) {
            $department = $user->coordinatorProfile?->department;
            $messages = Message::where(function ($query) use ($user, $department) {
                    $query->where('sender_id', $user->id)
                        ->orWhere('recipient_id', $user->id)
                        ->orWhere(function ($subQuery) use ($department) {
                            $subQuery->whereHas('sender.studentProfile', function ($studentQuery) use ($department) {
                                $studentQuery->where('department', $department);
                            });
                        });
                })
                ->with(['sender', 'recipient'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Other roles see all messages
            $messages = Message::with(['sender', 'recipient'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('messages.index', compact('messages'));
    }

    /**
     * Show the form for creating a new message
     */
    public function create()
    {
        $user = Auth::user();
        $recipients = collect();
        $selectedRecipient = null;
        $prefilledSubject = '';

        // Check for pre-filled recipient and subject from URL parameters
        if (request()->has('recipient')) {
            $selectedRecipient = User::find(request('recipient'));
        }
        
        // Handle reply_to parameter (for reply button)
        if (request()->has('reply_to')) {
            $selectedRecipient = User::find(request('reply_to'));
            // Get the original message to prefill subject
            if (request()->has('message_id')) {
                $originalMessage = Message::find(request('message_id'));
                if ($originalMessage) {
                    $prefilledSubject = str_starts_with($originalMessage->subject, 'Re: ') 
                        ? $originalMessage->subject 
                        : 'Re: ' . $originalMessage->subject;
                }
            }
        }
        
        if (request()->has('subject')) {
            $prefilledSubject = request('subject');
        }

        if ($user->isStudent()) {
            // Students can message their coordinator
            $coordinator = User::where('role', 'coordinator')
                ->whereHas('coordinatorProfile', function($query) use ($user) {
                    $query->where('department', $user->studentProfile?->department);
                })
                ->first();
            
            if ($coordinator) {
                $recipients->push($coordinator);
            }

            // Students can also message their supervisor if assigned
            if ($user->studentProfile?->supervisor_id) {
                $supervisor = User::find($user->studentProfile->supervisor_id);
                if ($supervisor) {
                    $recipients->push($supervisor);
                }
            }
        } elseif ($user->isCoordinator()) {
            $department = $user->coordinatorProfile?->department;

            // Coordinators can message students in their department
            $students = User::where('role', 'intern')
                ->whereHas('studentProfile', function($query) use ($department) {
                    $query->where('department', $department);
                })
                ->with('studentProfile')
                ->get();

            // Coordinators can also message supervisors handling their students
            $supervisors = User::where('role', 'supervisor')
                ->whereHas('studentProfiles', function($query) use ($department) {
                    $query->where('department', $department);
                })
                ->with('supervisorProfile')
                ->get();
            
            $recipients = $students->merge($supervisors)->unique('id');
        } elseif ($user->isSupervisor()) {
            // Supervisors can message their supervised students
            $students = User::where('role', 'intern')
                ->whereHas('studentProfile', function($query) use ($user) {
                    $query->where('supervisor_id', $user->id);
                })
                ->with('studentProfile')
                ->get();
            
            // Supervisors can also message coordinators of their students
            $coordinatorIds = $students->pluck('studentProfile.department')
                ->unique()
                ->map(function($department) {
                    return User::where('role', 'coordinator')
                        ->whereHas('coordinatorProfile', function($query) use ($department) {
                            $query->where('department', $department);
                        })
                        ->first();
                })
                ->filter()
                ->pluck('id')
                ->unique();
            
            $coordinators = User::whereIn('id', $coordinatorIds)->get();
            
            $recipients = $students->merge($coordinators);
        }

        return view('messages.create', compact('recipients', 'selectedRecipient', 'prefilledSubject'));
    }

    /**
     * Store a newly created message
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        // Check if user can send message to this recipient
        if (!$this->canSendMessageTo($user, $request->recipient_id)) {
            return back()->with('error', 'You are not authorized to send messages to this user.');
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);
        AuditLog::log(
            'message_sent',
            'User sent a message',
            'Message',
            $message->id,
            null,
            [
                'recipient_id' => (int) $message->recipient_id,
                'subject' => (string) $message->subject,
            ]
        );

        return redirect()->route('messages.index')->with('success', 'Message sent successfully!');
    }

    /**
     * Display the specified message
     */
    public function show(Message $message)
    {
        $user = Auth::user();

        // Check if user can view this message
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Mark as read if user is the recipient
        if ($message->recipient_id === $user->id && !$message->is_read) {
            $message->markAsRead();
            AuditLog::log(
                'message_read',
                'User read a message',
                'Message',
                $message->id,
                null,
                null
            );
        }

        return view('messages.show', compact('message'));
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message)
    {
        if ($message->recipient_id === Auth::id()) {
            $message->markAsRead();
            AuditLog::log(
                'message_read',
                'User marked message as read',
                'Message',
                $message->id,
                null,
                null
            );
        }

        return back();
    }

    /**
     * Mark message as unread
     */
    public function markAsUnread(Message $message)
    {
        if ($message->recipient_id === Auth::id()) {
            $message->markAsUnread();
            AuditLog::log(
                'message_unread',
                'User marked message as unread',
                'Message',
                $message->id,
                null,
                null
            );
        }

        return back();
    }

    /**
     * Delete a message
     */
    public function destroy(Message $message)
    {
        $user = Auth::user();

        // Users can only delete their own messages
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $old = $message->toArray();
        $message->delete();
        AuditLog::log(
            'message_deleted',
            'User deleted a message',
            'Message',
            $message->id,
            $old,
            null
        );

        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }

    /**
     * Check if user can send message to recipient
     */
    private function canSendMessageTo($sender, $recipientId)
    {
        $recipient = User::find($recipientId);
        
        if (!$recipient) {
            return false;
        }

        if ($sender->isStudent()) {
            // Students can message their coordinator or supervisor
            if ($recipient->isCoordinator()) {
                return $sender->studentProfile?->department === $recipient->coordinatorProfile?->department;
            }
            if ($recipient->isSupervisor()) {
                return $sender->studentProfile?->supervisor_id === $recipient->id;
            }
        } elseif ($sender->isCoordinator()) {
            // Coordinators can message students in their department
            if ($recipient->isStudent()) {
                return $sender->coordinatorProfile?->department === $recipient->studentProfile?->department;
            }
            // Coordinators can message supervisors who oversee their students
            if ($recipient->isSupervisor()) {
                return User::where('role', 'intern')
                    ->whereHas('studentProfile', function($query) use ($sender, $recipient) {
                        $query->where('department', $sender->coordinatorProfile?->department)
                              ->where('supervisor_id', $recipient->id);
                    })
                    ->exists();
            }
        } elseif ($sender->isSupervisor()) {
            // Supervisors can message their supervised students
            if ($recipient->isStudent()) {
                return $recipient->studentProfile?->supervisor_id === $sender->id;
            }
            // Supervisors can message coordinators of their students
            if ($recipient->isCoordinator()) {
                // Check if supervisor has any student in this coordinator's department
                $hasStudentInDepartment = User::where('role', 'intern')
                    ->whereHas('studentProfile', function($query) use ($sender, $recipient) {
                        $query->where('supervisor_id', $sender->id)
                              ->where('department', $recipient->coordinatorProfile?->department);
                    })
                    ->exists();
                return $hasStudentInDepartment;
            }
        }

        return false;
    }
}
