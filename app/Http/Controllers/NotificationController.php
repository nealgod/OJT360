<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
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
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->update(['read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(Notification $notification)
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->update(['read' => false]);

        return back()->with('success', 'Notification marked as unread.');
    }

    /**
     * Delete a notification for the authenticated user.
     */
    public function destroy(Notification $notification)
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted successfully.');
    }
}
