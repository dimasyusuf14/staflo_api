<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\Bucket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Get available task statuses
     */
    public function statuses()
    {
        return response()->json([
            'message' => 'Daftar status task berhasil diambil',
            'data' => [
                [
                    'id' => 1,
                    'code' => 'belum_dimulai',
                    'name' => 'Belum dimulai',
                ],
                [
                    'id' => 2,
                    'code' => 'dalam_pengerjaan',
                    'name' => 'Dalam pengerjaan',
                ],
                [
                    'id' => 3,
                    'code' => 'selesai',
                    'name' => 'Selesai',
                ],
            ],
        ], 200);
    }

    /**
     * Get available task priorities
     */
    public function priorities()
    {
        return response()->json([
            'message' => 'Daftar prioritas task berhasil diambil',
            'data' => [
                [
                    'id' => 1,
                    'code' => 'urgent',
                    'name' => 'Urgent',
                ],
                [
                    'id' => 2,
                    'code' => 'important',
                    'name' => 'Important',
                ],
                [
                    'id' => 3,
                    'code' => 'medium',
                    'name' => 'Medium',
                ],
                [
                    'id' => 4,
                    'code' => 'low',
                    'name' => 'Low',
                ],
            ],
        ], 200);
    }

    /**
     * Get all tasks for current user
     */
    public function index(Request $request)
    {
        $authUser = $request->user();
        $userLevel = $authUser->getLevel();

        $query = Task::with('assignee:id,name,position_id', 'assigner:id,name', 'bucket:id,name', 'subtasks:id,task_id,title,is_completed', 'attachments:id,task_id,uploaded_by,original_name,file_path,file_type,file_size');

        // Filter based on user level
        if ($userLevel === 3) {
            // Staff hanya lihat task yang di-assign ke mereka
            $query->where('assigned_to', $authUser->id);
        } elseif ($userLevel === 2) {
            // Manager lihat task yang di-create mereka atau task untuk staff mereka
            $query->where(function ($q) use ($authUser) {
                $q->where('assigned_by', $authUser->id)
                    ->orWhere('assigned_to', $authUser->id);
            });
        }
        // Level 1 (Director) dapat lihat semua task

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Daftar task berhasil diambil',
            'data' => $tasks,
            'total' => $tasks->count(),
        ], 200);
    }

    /**
     * Create new task (Director & Manager only - Level 1 & 2)
     */
    public function store(Request $request)
    {
        $authUser = $request->user();
        $userLevel = $authUser->getLevel();

        if ($request->filled('priority')) {
            $request->merge(['priority' => strtolower((string) $request->priority)]);
        }

        // Hanya level 1 (Director) dan level 2 (Manager) bisa create task
        if (!in_array($userLevel, [1, 2])) {
            throw ValidationException::withMessages([
                'authorization' => ['Hanya Direktur dan Manager yang dapat membuat task.'],
            ]);
        }

        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'assigned_to' => 'required|exists:users,id',
            'bucket_id' => 'required|exists:buckets,id',
            'status' => 'nullable|in:belum_dimulai,dalam_pengerjaan,selesai',
            'priority' => 'nullable|in:urgent,important,medium,low',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'subtasks' => 'nullable|array',
            'subtasks.*.title' => 'required|string|max:255',
            'subtasks.*.is_completed' => 'nullable|boolean',
        ]);

        // Check authorization: Manager can only assign to staff
        if ($userLevel === 2) {
            $assignee = \App\Models\User::find($validated['assigned_to']);
            if ($assignee->getLevel() !== 3) {
                throw ValidationException::withMessages([
                    'assigned_to' => ['Manager hanya dapat assign task ke Staff.'],
                ]);
            }
        }

        $task = DB::transaction(function () use ($validated, $authUser) {
            $createdTask = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'assigned_to' => $validated['assigned_to'],
                'assigned_by' => $authUser->id,
                'bucket_id' => $validated['bucket_id'],
                'status' => $validated['status'] ?? 'belum_dimulai',
                'priority' => $validated['priority'] ?? 'medium',
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ]);

            if (!empty($validated['subtasks'])) {
                $subtasks = collect($validated['subtasks'])->map(function ($subtask) {
                    return [
                        'title' => $subtask['title'],
                        'is_completed' => $subtask['is_completed'] ?? false,
                    ];
                })->toArray();

                $createdTask->subtasks()->createMany($subtasks);
            }

            return $createdTask;
        });

        return response()->json([
            'message' => 'Task berhasil dibuat',
            'data' => $task->load('assignee:id,name', 'assigner:id,name', 'bucket:id,name', 'subtasks:id,task_id,title,is_completed', 'attachments:id,task_id,uploaded_by,original_name,file_path,file_type,file_size'),
        ], 201);
    }

    /**
     * Update task details
     */
    public function update(Request $request, $id)
    {
        $authUser = $request->user();
        $task = Task::find($id);

        if ($request->filled('priority')) {
            $request->merge(['priority' => strtolower((string) $request->priority)]);
        }

        if (!$task) {
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        // Check authorization: Only creator or Director can update full task details
        if ($task->assigned_by !== $authUser->id && !$authUser->isDirector()) {
            throw ValidationException::withMessages([
                'authorization' => ['Anda tidak memiliki izin mengupdate task ini.'],
            ]);
        }

        // Validate partial update input
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'assigned_to' => 'sometimes|required|exists:users,id',
            'bucket_id' => 'sometimes|required|exists:buckets,id',
            'status' => 'sometimes|required|in:belum_dimulai,dalam_pengerjaan,selesai',
            'priority' => 'sometimes|required|in:urgent,important,medium,low',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
            'subtasks' => 'sometimes|array',
            'subtasks.*.title' => 'required|string|max:255',
            'subtasks.*.is_completed' => 'nullable|boolean',
        ]);

        // Manager can only assign task to staff
        if (array_key_exists('assigned_to', $validated) && $authUser->getLevel() === 2) {
            $assignee = \App\Models\User::find($validated['assigned_to']);
            if ($assignee && $assignee->getLevel() !== 3) {
                throw ValidationException::withMessages([
                    'assigned_to' => ['Manager hanya dapat assign task ke Staff.'],
                ]);
            }
        }

        DB::transaction(function () use ($task, $validated) {
            $taskData = collect($validated)->except('subtasks')->toArray();
            if (!empty($taskData)) {
                $task->update($taskData);
            }

            if (array_key_exists('subtasks', $validated)) {
                $task->subtasks()->delete();

                if (!empty($validated['subtasks'])) {
                    $subtasks = collect($validated['subtasks'])->map(function ($subtask) {
                        return [
                            'title' => $subtask['title'],
                            'is_completed' => $subtask['is_completed'] ?? false,
                        ];
                    })->toArray();

                    $task->subtasks()->createMany($subtasks);
                }
            }
        });

        return response()->json([
            'message' => 'Task berhasil diupdate',
            'data' => $task->load('assignee:id,name', 'assigner:id,name', 'bucket:id,name', 'subtasks:id,task_id,title,is_completed', 'attachments:id,task_id,uploaded_by,original_name,file_path,file_type,file_size'),
        ], 200);
    }

    /**
     * Update task description by assignee, creator, or Director
     */
    public function updateDescription(Request $request, $id)
    {
        $authUser = $request->user();
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        if ($task->assigned_to !== $authUser->id && $task->assigned_by !== $authUser->id && !$authUser->isDirector()) {
            throw ValidationException::withMessages([
                'authorization' => ['Anda tidak memiliki izin mengupdate deskripsi task ini.'],
            ]);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        $task->update([
            'description' => $validated['description'],
        ]);

        return response()->json([
            'message' => 'Deskripsi task berhasil diupdate',
            'data' => $task->load('assignee:id,name', 'assigner:id,name', 'bucket:id,name', 'subtasks:id,task_id,title,is_completed', 'attachments:id,task_id,uploaded_by,original_name,file_path,file_type,file_size'),
        ], 200);
    }

    /**
     * Update assignee task changes in one request
     */
    public function assigneeSave(Request $request, $id)
    {
        $authUser = $request->user();
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        if ($task->assigned_to !== $authUser->id && $task->assigned_by !== $authUser->id && !$authUser->isDirector()) {
            throw ValidationException::withMessages([
                'authorization' => ['Anda tidak memiliki izin mengupdate task ini.'],
            ]);
        }

        $request->validate([
            'status' => 'sometimes|required|in:belum_dimulai,dalam_pengerjaan,selesai',
            'description' => 'sometimes|nullable|string|max:1000',
            'file' => 'sometimes|file|mimes:doc,docx,pdf,xls,xlsx,jpg,jpeg,png|max:10240',
            'files' => 'sometimes|array',
            'files.*' => 'file|mimes:doc,docx,pdf,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        $subtasksInput = $request->input('subtasks');
        $subtasksPayload = null;

        if (is_string($subtasksInput) && trim($subtasksInput) !== '') {
            $decoded = json_decode($subtasksInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    'subtasks' => ['Format subtasks harus JSON array yang valid.'],
                ]);
            }
            $subtasksPayload = $decoded;
        } elseif (is_array($subtasksInput)) {
            $subtasksPayload = $subtasksInput;
        }

        if ($subtasksPayload !== null) {
            $subtaskValidator = Validator::make([
                'subtasks' => $subtasksPayload,
            ], [
                'subtasks' => 'array',
                'subtasks.*.id' => 'required|integer',
                'subtasks.*.is_completed' => 'required|boolean',
            ]);

            if ($subtaskValidator->fails()) {
                throw ValidationException::withMessages($subtaskValidator->errors()->toArray());
            }
        }

        $singleFile = $request->file('file');
        $multipleFiles = $request->file('files', []);
        $uploadedFiles = array_values(array_filter(array_merge(
            $singleFile ? [$singleFile] : [],
            is_array($multipleFiles) ? $multipleFiles : []
        )));

        $hasTaskData = $request->has('status') || $request->has('description');
        $hasSubtaskData = $subtasksPayload !== null;
        $hasFileData = !empty($uploadedFiles);

        if (!$hasTaskData && !$hasSubtaskData && !$hasFileData) {
            throw ValidationException::withMessages([
                'payload' => ['Tidak ada data yang dikirim untuk disimpan.'],
            ]);
        }

        DB::transaction(function () use ($request, $task, $subtasksPayload, $uploadedFiles, $authUser) {
            $taskData = [];

            if ($request->has('status')) {
                $taskData['status'] = $request->input('status');
            }

            if ($request->has('description')) {
                $taskData['description'] = $request->input('description');
            }

            if (!empty($taskData)) {
                $task->update($taskData);
            }

            if ($subtasksPayload !== null) {
                foreach ($subtasksPayload as $subtaskInput) {
                    $subtask = Subtask::where('task_id', $task->id)
                        ->where('id', $subtaskInput['id'])
                        ->first();

                    if (!$subtask) {
                        throw ValidationException::withMessages([
                            'subtasks' => ["Subtask dengan id {$subtaskInput['id']} tidak ditemukan pada task ini."],
                        ]);
                    }

                    $subtask->update([
                        'is_completed' => $subtaskInput['is_completed'],
                    ]);
                }
            }

            foreach ($uploadedFiles as $uploadedFile) {
                $storedPath = $uploadedFile->store('task_attachments', 'public');

                $task->attachments()->create([
                    'uploaded_by' => $authUser->id,
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'file_path' => $storedPath,
                    'file_type' => $uploadedFile->getClientMimeType(),
                    'file_size' => $uploadedFile->getSize(),
                ]);
            }
        });

        return response()->json([
            'message' => 'Perubahan task berhasil disimpan',
            'data' => $task->fresh()->load('assignee:id,name', 'assigner:id,name', 'bucket:id,name', 'subtasks:id,task_id,title,is_completed', 'attachments:id,task_id,uploaded_by,original_name,file_path,file_type,file_size'),
        ], 200);
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, $id)
    {
        $authUser = $request->user();
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        // Check authorization: Can update own task or if creator
        if ($task->assigned_to !== $authUser->id && $task->assigned_by !== $authUser->id && !$authUser->isDirector()) {
            throw ValidationException::withMessages([
                'authorization' => ['Anda tidak memiliki izin mengupdate task ini.'],
            ]);
        }

        // Validate input
        $validated = $request->validate([
            'status' => 'required|in:belum_dimulai,dalam_pengerjaan,selesai',
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Status task berhasil diupdate',
            'data' => $task->load('assignee:id,name', 'assigner:id,name', 'bucket:id,name', 'subtasks:id,task_id,title,is_completed', 'attachments:id,task_id,uploaded_by,original_name,file_path,file_type,file_size'),
        ], 200);
    }

    /**
     * Update completion status for a subtask
     */
    public function updateSubtaskStatus(Request $request, $taskId, $subtaskId)
    {
        $authUser = $request->user();
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        if ($task->assigned_to !== $authUser->id && $task->assigned_by !== $authUser->id && !$authUser->isDirector()) {
            throw ValidationException::withMessages([
                'authorization' => ['Anda tidak memiliki izin mengupdate subtask ini.'],
            ]);
        }

        $validated = $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        $subtask = Subtask::where('task_id', $task->id)->find($subtaskId);
        if (!$subtask) {
            return response()->json([
                'message' => 'Subtask tidak ditemukan',
            ], 404);
        }

        $subtask->update([
            'is_completed' => $validated['is_completed'],
        ]);

        return response()->json([
            'message' => 'Status subtask berhasil diupdate',
            'data' => $task->fresh()->load('assignee:id,name', 'assigner:id,name', 'bucket:id,name', 'subtasks:id,task_id,title,is_completed', 'attachments:id,task_id,uploaded_by,original_name,file_path,file_type,file_size'),
        ], 200);
    }

    /**
     * Upload task attachment by assignee, creator, or Director
     */
    public function uploadAttachment(Request $request, $id)
    {
        $authUser = $request->user();
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        if ($task->assigned_to !== $authUser->id && $task->assigned_by !== $authUser->id && !$authUser->isDirector()) {
            throw ValidationException::withMessages([
                'authorization' => ['Anda tidak memiliki izin mengunggah lampiran untuk task ini.'],
            ]);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:doc,docx,pdf,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        $uploadedFile = $validated['file'];
        $storedPath = $uploadedFile->store('task_attachments', 'public');

        $task->attachments()->create([
            'uploaded_by' => $authUser->id,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $storedPath,
            'file_type' => $uploadedFile->getClientMimeType(),
            'file_size' => $uploadedFile->getSize(),
        ]);

        return response()->json([
            'message' => 'Lampiran task berhasil diupload',
            'data' => $task->fresh()->load('assignee:id,name', 'assigner:id,name', 'bucket:id,name', 'subtasks:id,task_id,title,is_completed', 'attachments:id,task_id,uploaded_by,original_name,file_path,file_type,file_size'),
        ], 200);
    }

    /**
     * Delete task (Only creator or Director)
     */
    public function destroy(Request $request, $id)
    {
        $authUser = $request->user();
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        // Check authorization: Only creator or Director can delete
        if ($task->assigned_by !== $authUser->id && !$authUser->isDirector()) {
            throw ValidationException::withMessages([
                'authorization' => ['Anda tidak memiliki izin menghapus task ini.'],
            ]);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task berhasil dihapus',
            'data' => $task,
        ], 200);
    }
}
