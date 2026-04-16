<?php

namespace App\Http\Controllers;

use App\Models\StaffNotification;
use App\Models\StaffNotificationRead;
use Illuminate\Http\Request;

class StaffNotificationController extends Controller
{
    /**
     * JSON endpoint — returns unread count + recent 10 for the bell dropdown.
     */
    public function unread()
    {
        $user = auth()->user();
        $role = $user->role->name ?? null;

        $notifications = StaffNotification::with('labOrder.patient')
            ->forRole($role)
            ->unreadBy($user->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'order_url'  => route('lab-orders.show', $n->lab_order_id),
                'created_at' => $n->created_at->format('d M Y, h:i A'),
            ]);

        $count = StaffNotification::forRole($role)
            ->unreadBy($user->id)
            ->count();

        return response()->json([
            'count'         => $count,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(StaffNotification $staffNotification)
    {
        StaffNotificationRead::firstOrCreate([
            'staff_notification_id' => $staffNotification->id,
            'user_id'               => auth()->id(),
        ], [
            'read_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Mark all visible notifications as read for the current user.
     */
    public function markAllRead()
    {
        $user = auth()->user();
        $role = $user->role->name ?? null;

        $ids = StaffNotification::forRole($role)
            ->unreadBy($user->id)
            ->pluck('id');

        foreach ($ids as $id) {
            StaffNotificationRead::firstOrCreate([
                'staff_notification_id' => $id,
                'user_id'               => $user->id,
            ], [
                'read_at' => now(),
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
