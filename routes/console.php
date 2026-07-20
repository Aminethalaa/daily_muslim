<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
| Driven by a single cPanel cron entry running every minute:
|   * * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
*/

// Per-prayer push reminders — checks each user's local prayer times each minute.
Schedule::command('saout:send-prayer-reminders')
    ->everyMinute()
    ->withoutOverlapping();

// Daily recap email — runs hourly; each user is sent at their chosen local hour.
Schedule::command('saout:send-recap')
    ->hourly()
    ->withoutOverlapping();

// Process the queue (emails/push) without a long-running worker — shared-host friendly.
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
