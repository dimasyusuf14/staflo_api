<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send task due-date reminders:
//   12 hours before end-of-day → runs at 12:00 (noon)
//   8 hours before end-of-day  → runs at 16:00
//   6 hours before end-of-day  → runs at 18:00
Schedule::command('app:send-task-due-reminders --hours=12')->dailyAt('12:00');
Schedule::command('app:send-task-due-reminders --hours=8')->dailyAt('16:00');
Schedule::command('app:send-task-due-reminders --hours=6')->dailyAt('18:00');
