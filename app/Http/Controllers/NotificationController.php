<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        return view('notifications.create');
    }

    /**
     * Store a newly created notification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
        ]);

        // Find coordinator for student's department
        $coordinator = User::where('role', 'coordinator')
            ->whereHas('coordinatorProfile', function($query) use ($request) {
                $query->where('department', auth()->user()->studentProfile->department);
            })
            ->first();

        if (!$coordinator) {
            return back()->with('error', 'No coordinator found for your department.');
        }

        Notification::create([
            'user_id' => $coordinator->id,
            'type' => $request->type,
            'title' => $request->title,
            'message' => $request->message,
            'data' => $request->data,
        ]);

        return back()->with('success', 'Notification sent to your coordinator successfully!');
    }

    /**
     * Display notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update(['read' => true]);
        }

        return back();
    }
}
