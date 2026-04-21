<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskCommentController extends Controller
{
    /**
     * Get all comments for a task
     */
    public function index(Request $request, $taskId)
    {
        $authUser = $request->user();
        $userLevel = $authUser->getLevel();

        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        // Authorization: only allow users related to the task
        if ($userLevel === 3 && $task->assigned_to !== $authUser->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        if ($userLevel === 2 && $task->assigned_by !== $authUser->id && $task->assigned_to !== $authUser->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $comments = TaskComment::with('user:id,name,profile_photo')
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'task_id' => $comment->task_id,
                    'user_id' => $comment->user_id,
                    'user_name' => $comment->user?->name,
                    'user_photo' => $comment->user?->profile_photo_url,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at->format('F j, Y \a\t g:i A'),
                    'created_at_raw' => $comment->created_at,
                ];
            });

        return response()->json([
            'message' => 'Komentar berhasil diambil',
            'data' => $comments,
            'total' => $comments->count(),
        ], 200);
    }

    /**
     * Store a new comment for a task
     */
    public function store(Request $request, $taskId)
    {
        $authUser = $request->user();
        $userLevel = $authUser->getLevel();

        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        // Authorization: only users related to the task can comment
        if ($userLevel === 3 && $task->assigned_to !== $authUser->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        if ($userLevel === 2 && $task->assigned_by !== $authUser->id && $task->assigned_to !== $authUser->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $comment = TaskComment::create([
            'task_id' => $taskId,
            'user_id' => $authUser->id,
            'comment' => $validated['comment'],
        ]);

        // Notify everyone involved in the task except the commenter
        $recipients = collect([$task->assigned_by, $task->assigned_to])
            ->filter(fn($id) => $id && $id !== $authUser->id)
            ->unique();

        foreach ($recipients as $recipientId) {
            AppNotification::notify(
                $recipientId,
                'task_commented',
                $authUser->name . ' berkomentar pada task "' .  $task->title . '"',
                '"' . $validated['comment'] . '"',
                ['task_id' => $task->id, 'task_title' => $task->title, 'comment_id' => $comment->id],
                $authUser->id
            );
        }

        $comment->load('user:id,name,profile_photo');

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan',
            'data' => [
                'id' => $comment->id,
                'task_id' => $comment->task_id,
                'user_id' => $comment->user_id,
                'user_name' => $comment->user?->name,
                'user_photo' => $comment->user?->profile_photo_url,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at->format('F j, Y \a\t g:i A'),
                'created_at_raw' => $comment->created_at,
            ],
        ], 201);
    }

    /**
     * Delete a comment
     */
    public function destroy(Request $request, $taskId, $commentId)
    {
        $authUser = $request->user();

        $comment = TaskComment::where('task_id', $taskId)->where('id', $commentId)->first();

        if (!$comment) {
            return response()->json(['message' => 'Komentar tidak ditemukan'], 404);
        }

        // Only the comment owner or director can delete
        if ($comment->user_id !== $authUser->id && $authUser->getLevel() !== 1) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus'], 200);
    }
}
