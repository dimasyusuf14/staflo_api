<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDueReminderEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Task $task,
        public readonly User $assignee,
        public readonly int  $hoursLeft,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder: Task \"{$this->task->title}\" jatuh tempo hari ini",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'due_date_reminder.SendTaskDueReminders',
            with: [
                'task'       => $this->task,
                'assignee'   => $this->assignee,
                'hoursLeft'  => $this->hoursLeft,
                'bucketName' => $this->task->relationLoaded('bucket') ? $this->task->bucket?->name : null,
                'dueDate'    => $this->task->end_date?->translatedFormat('d F Y') ?? $this->task->end_date,
                'priority'   => $this->task->priority,
            ],
        );
    }
}
