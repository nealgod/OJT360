<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display messages for the authenticated user
     */
    /**
     * Display conversations for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Get all messages where user is sender or recipient
        $allMessages = Message::where(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->orWhere('recipient_id', $userId);
        })
        ->with(['sender', 'recipient'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Group by conversation (the other user)
        $conversations = $allMessages->groupBy(function($message) use ($userId) {
            return $message->sender_id === $userId ? $message->recipient_id : $message->sender_id;
        })->map(function($messages) use ($userId) {
            $otherUser = $messages->first()->sender_id === $userId 
                ? $messages->first()->recipient 
                : $messages->first()->sender;
            
            // Count unread messages from this user
            $unreadCount = $messages->where('recipient_id', $userId)->where('is_read', false)->count();
            
            return [
                'user' => $otherUser,
                'last_message' => $messages->first(),
                'unread_count' => $unreadCount
            ];
        });

        return view('messages.index', compact('conversations'));
    }

    /**
     * Display chat with a specific user
     */
    public function chat(User $user)
    {
        $currentUser = Auth::user();

        // Check if user can message this person (or if they have history)
        // We allow viewing history even if they can't send new messages (e.g. if relationship changed)
        // But for now, let's strictly enforce the permission for consistency or just check history
        
        $hasHistory = Message::where(function($q) use ($currentUser, $user) {
            $q->where('sender_id', $currentUser->id)->where('recipient_id', $user->id);
        })->orWhere(function($q) use ($currentUser, $user) {
            $q->where('sender_id', $user->id)->where('recipient_id', $currentUser->id);
        })->exists();

        if (!$hasHistory && !$this->canSendMessageTo($currentUser, $user->id)) {
             return redirect()->route('messages.index')->with('error', 'You cannot start a conversation with this user.');
        }

        // Mark messages from this user as read
        Message::where('sender_id', $user->id)
            ->where('recipient_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Get conversation history
        $messages = Message::where(function($q) use ($currentUser, $user) {
            $q->where('sender_id', $currentUser->id)->where('recipient_id', $user->id);
        })->orWhere(function($q) use ($currentUser, $user) {
            $q->where('sender_id', $user->id)->where('recipient_id', $currentUser->id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return view('messages.chat', compact('user', 'messages'));
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
                        : 'Re: '.$originalMessage->subject;
                }
            }
        }

        if (request()->has('subject')) {
            $prefilledSubject = request('subject');
        }

        if ($user->isStudent()) {
            // Students can message their coordinator
            $coordinator = User::where('role', 'coordinator')
                ->whereHas('coordinatorProfile', function ($query) use ($user) {
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
                ->whereHas('studentProfile', function ($query) use ($department) {
                    $query->where('department', $department);
                })
                ->with('studentProfile')
                ->get();

            // Coordinators can also message supervisors handling their students
            $supervisors = User::where('role', 'supervisor')
                ->whereHas('studentProfiles', function ($query) use ($department) {
                    $query->where('department', $department);
                })
                ->with('supervisorProfile')
                ->get();

            $recipients = $students->merge($supervisors)->unique('id');
        } elseif ($user->isSupervisor()) {
            // Supervisors can message their supervised students
            $students = User::where('role', 'intern')
                ->whereHas('studentProfile', function ($query) use ($user) {
                    $query->where('supervisor_id', $user->id);
                })
                ->with('studentProfile')
                ->get();

            // Supervisors can also message coordinators of their students
            $coordinatorIds = $students->pluck('studentProfile.department')
                ->unique()
                ->map(function ($department) {
                    return User::where('role', 'coordinator')
                        ->whereHas('coordinatorProfile', function ($query) use ($department) {
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

        $userDepartment = null;
        if ($user->isStudent()) {
            $userDepartment = $user->studentProfile?->department;
        } elseif ($user->isCoordinator()) {
            $userDepartment = $user->coordinatorProfile?->department;
        }

        return view('messages.create', compact('recipients', 'selectedRecipient', 'prefilledSubject', 'userDepartment'));
    }

    /**
     * Store a newly created message
     */
    public function store(Request $request)
    {
        $request->merge([
            'subject' => $request->input('subject', 'Message')
        ]);

        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        // Check if user can send message to this recipient
        if (! $this->canSendMessageTo($user, $request->recipient_id)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'You are not authorized to send messages to this user.'], 403);
            }
            return back()->with('error', 'You are not authorized to send messages to this user.');
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        AuditLog::log('message_sent', 'Message sent', 'Message', $message->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender')
            ]);
        }

        return redirect()->route('messages.chat', $request->recipient_id);
    }

    /**
     * Display the specified message
     */
    public function show(Message $message)
    {
        $user = Auth::user();

        // Check if user can view this message
        if ($message->sender_id != $user->id && $message->recipient_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        // Mark as read if user is the recipient
        if ($message->recipient_id == $user->id && ! $message->is_read) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message)
    {
        if ($message->recipient_id == Auth::id()) {
            $message->markAsRead();
            AuditLog::log('message_read', 'Message read', 'Message', $message->id);
        }

        return back();
    }

    /**
     * Mark message as unread
     */
    public function markAsUnread(Message $message)
    {
        if ($message->recipient_id == Auth::id()) {
            $message->markAsUnread();
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
        if ($message->sender_id != $user->id && $message->recipient_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        $message->delete();

        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }

    /**
     * Check if user can send message to recipient
     */
    private function canSendMessageTo($sender, $recipientId)
    {
        $recipient = User::find($recipientId);

        if (! $recipient) {
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
                    ->whereHas('studentProfile', function ($query) use ($sender, $recipient) {
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
                    ->whereHas('studentProfile', function ($query) use ($sender, $recipient) {
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
