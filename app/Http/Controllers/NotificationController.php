<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Services\FcmService;
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

    /**
     * Update or remove the authenticated user's FCM device token.
     * Called from Flutter after login (or when token refreshes).
     * POST /notifications/fcm-token  { "fcm_token": "<token>" }
     * DELETE equivalent: send { "fcm_token": null }
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => ['nullable', 'string', 'max:255'],
        ]);

        $newToken = $request->input('fcm_token');

        // Lepas token dari user lain yang mungkin masih menyimpan token yang sama
        // (terjadi jika logout sebelumnya tidak menghapus token)
        if ($newToken) {
            \App\Models\User::where('fcm_token', $newToken)
                ->where('id', '!=', $request->user()->id)
                ->update(['fcm_token' => null]);
        }

        $request->user()->update([
            'fcm_token' => $newToken,
        ]);

        return response()->json([
            'message' => 'FCM token berhasil diperbarui',
        ], 200);
    }

    /**
     * Test: send a push notification to the authenticated user's own device.
     * POST /notifications/test-push
     */
    public function testPush(Request $request)
    {
        $user = $request->user();

        if (! $user->fcm_token) {
            return response()->json([
                'message' => 'FCM token belum terdaftar. Silakan update FCM token terlebih dahulu.',
            ], 422);
        }

        $success = app(FcmService::class)->sendToDevice(
            $user->fcm_token,
            'Test Notifikasi',
            'Push notification dari Staflo API berjalan dengan baik! 🎉',
            ['type' => 'test']
        );

        return response()->json([
            'message' => $success ? 'Push notification berhasil dikirim' : 'Gagal mengirim push notification',
            'success' => $success,
        ], $success ? 200 : 500);
    }

    private function resolveIcon(string $type): string
    {
        return match (true) {
            str_starts_with($type, 'task_due_reminder_') => 'warning',
            default => 'notification',
        };
    }

    private function format(AppNotification $n): array
    {
        return [
            'id' => $n->id,
            'type' => $n->type,
            'icon' => $this->resolveIcon($n->type),
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
