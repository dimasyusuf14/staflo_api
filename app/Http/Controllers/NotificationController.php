<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     * Query params: ?unread=1 to filter only unread
     */
    public function index(Request $request)
    {
        $authUser = $request->user();

        $query = AppNotification::with('actor:id,name,profile_photo')
            ->where('user_id', $authUser->id)
            ->orderBy('created_at', 'desc');

        if ($request->boolean('unread')) {
            $query->whereNull('read_at');
        }

        $notifications = $query->get()->map(fn($n) => $this->format($n));

        return response()->json([
            'message' => 'Notifikasi berhasil diambil',
            'data' => $notifications,
            'total' => $notifications->count(),
        ], 200);
    }

    /**
     * Get unread notification count for the authenticated user.
     */
    public function unreadCount(Request $request)
    {
        $count = AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'message' => 'Jumlah notifikasi belum dibaca berhasil diambil',
            'unread_count' => $count,
        ], 200);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, int $id)
    {
        $notification = AppNotification::where('user_id', $request->user()->id)->find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        if (!$notification->isRead()) {
            $notification->update(['read_at' => now()]);
        }

        $notification->load('actor:id,name,profile_photo');

        return response()->json([
            'message' => 'Notifikasi berhasil ditandai sudah dibaca',
            'data' => $this->format($notification),
        ], 200);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request)
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Semua notifikasi berhasil ditandai sudah dibaca',
        ], 200);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, int $id)
    {
        $notification = AppNotification::where('user_id', $request->user()->id)->find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notifikasi berhasil dihapus',
        ], 200);
    }

    private function format(AppNotification $n): array
    {
        return [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'body' => $n->body,
            'data' => $n->data,
            'actor_id' => $n->actor_id,
            'actor_name' => $n->actor?->name,
            'actor_photo' => $n->actor?->profile_photo_url,
            'is_read' => $n->isRead(),
            'read_at' => $n->read_at?->toIso8601String(),
            'created_at' => $n->created_at->toIso8601String(),
        ];
    }
}
