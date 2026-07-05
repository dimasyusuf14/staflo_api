<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Preview email reminder — HAPUS setelah selesai testing
Route::get('/preview/task-due-reminder', function () {
    $task = Task::with(['assignees', 'bucket'])->whereHas('assignees')->latest()->first()
        ?? new Task([
            'title'       => 'Desain UI Halaman Dashboard',
            'description' => 'Membuat desain antarmuka untuk halaman dashboard aplikasi mobile Staflo sesuai wireframe yang sudah disetujui.',
            'end_date'    => now()->toDateString(),
            'priority'    => 'tinggi',
        ]);

    $assignee = $task->assignees->first()
        ?? new User(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);

    $bucketName = $task->relationLoaded('bucket') ? $task->bucket?->name : 'Pengembangan Aplikasi';
    $dueDate    = $task->end_date?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y');

    return view('due_date_reminder.SendTaskDueReminders', [
        'task'      => $task,
        'assignee'  => $assignee,
        'hoursLeft' => 6,
        'bucketName' => $bucketName,
        'dueDate'   => $dueDate,
        'priority'  => $task->priority,
    ]);
});
