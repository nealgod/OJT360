<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            // Coordinators see messages from students in their department
            $department = $user->coordinatorProfile?->department;
            $messages = Message::whereHas('sender.studentProfile', function($query) use ($department) {
                    $query->where('department', $department);
                })
                ->orWhere('recipient_id', $user->id)
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
            // Coordinators can message students in their department
            $students = User::where('role', 'intern')
                ->whereHas('studentProfile', function($query) use ($user) {
                    $query->where('department', $user->coordinatorProfile?->department);
                })
                ->with('studentProfile')
                ->get();
            
            $recipients = $students;
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

        Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

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

        $message->delete();

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
        }

        return false;
    }
}