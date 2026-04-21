<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendTaskDueReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Acceptable hours (based on server/app timezone):
     *   --hours=12  → runs at 12:00, reminds tasks due at end of today (12h before midnight)
     *   --hours=8   → runs at 16:00, reminds tasks due at end of today (8h before midnight)
     *
     * @var string
     */
    protected $signature = 'app:send-task-due-reminders {--hours=12 : How many hours before end-of-day to send the reminder (8 or 12)}';

    protected $description = 'Send push & in-app notifications for tasks whose due date is today, 8 or 12 hours before end-of-day';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        if (!in_array($hours, [8, 12])) {
            $this->error('--hours must be 8 or 12.');
            return self::FAILURE;
        }

        $today = Carbon::today()->toDateString();
        $notificationType = "task_due_reminder_{$hours}h";

        // Find incomplete tasks due today that still have an assignee
        $tasks = Task::with('assignee:id,name,fcm_token')
            ->whereDate('end_date', $today)
            ->where('status', '!=', 'selesai')
            ->whereNotNull('assigned_to')
            ->get();

        $sent = 0;

        foreach ($tasks as $task) {
            // Skip if this exact reminder was already sent for this task
            $alreadySent = AppNotification::where('user_id', $task->assigned_to)
                ->where('type', $notificationType)
                ->whereJsonContains('data->task_id', $task->id)
                ->exists();

            if ($alreadySent) {
                continue;
            }


            AppNotification::notify(
                $task->assigned_to,
                $notificationType,
                'Reminder: Dateline tugas hari ini',
                "Task \"{$task->title}\" akan jatuh tempo hari ini.",
                ['task_id' => $task->id, 'task_title' => $task->title],
            );

            $sent++;
        }

        $this->info("Reminder ({$hours}h) sent for {$sent} task(s) due on {$today}.");

        return self::SUCCESS;
    }
}
