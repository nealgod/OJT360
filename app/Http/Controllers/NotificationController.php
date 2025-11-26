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
        if ($notification->user_id === auth()->id()) {
            $notification->update(['read' => true]);
        }

        return back();
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(Notification $notification)
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update(['read' => false]);
        }

        return back();
    }

    /**
     * Delete a notification for the authenticated user.
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id === auth()->id()) {
            $notification->delete();
        }

        return back();
    }
}
