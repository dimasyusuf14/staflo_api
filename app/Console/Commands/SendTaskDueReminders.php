<?php

namespace App\Console\Commands;

use App\Mail\TaskDueReminderEmail;
use App\Models\AppNotification;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendTaskDueReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Acceptable hours (based on server/app timezone):
     *   --hours=12  → runs at 12:00, reminds tasks due at end of today (12h before midnight)
     *   --hours=8   → runs at 16:00, reminds tasks due at end of today (8h before midnight)
     *   --hours=6   → runs at 18:00, reminds tasks due at end of today (6h before midnight)
     *
     * @var string
     */
    protected $signature = 'app:send-task-due-reminders {--hours=12 : How many hours before end-of-day to send the reminder (6, 8, or 12)}';

    protected $description = 'Send push & in-app notifications for tasks whose due date is today, 6, 8, or 12 hours before end-of-day';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        if (!in_array($hours, [6, 8, 12])) {
            $this->error('--hours must be 6, 8, or 12.');
            return self::FAILURE;
        }

        $today = Carbon::today()->toDateString();
        $notificationType = "task_due_reminder_{$hours}h";

        // Find incomplete tasks due today that have at least one assignee
        $tasks = Task::with(['assignees:id,name,email,fcm_token', 'bucket:id,name'])
            ->whereDate('end_date', $today)
            ->where('status', '!=', 'selesai')
            ->whereHas('assignees')
            ->get();

        $sent = 0;

        foreach ($tasks as $task) {
            foreach ($task->assignees as $assignee) {
                // Skip if this exact reminder was already sent to this user for this task
                $alreadySent = AppNotification::where('user_id', $assignee->id)
                    ->where('type', $notificationType)
                    ->whereJsonContains('data->task_id', (string) $task->id)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                AppNotification::notify(
                    $assignee->id,
                    $notificationType,
                    'Reminder: Dateline tugas hari ini',
                    "Task \"{$task->title}\" akan jatuh tempo hari ini.",
                    ['task_id' => (string) $task->id, 'task_title' => $task->title],
                );

                if ($assignee->email) {
                    Mail::to($assignee->email)->queue(
                        new TaskDueReminderEmail($task, $assignee, $hours)
                    );
                }

                $sent++;
            }
        }

        $this->info("Reminder ({$hours}h) sent for {$sent} assignee(s) on {$today}.");

        return self::SUCCESS;
    }
}
