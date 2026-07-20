<?php

namespace App\Console\Commands;

use App\Mail\DailyRecap;
use App\Models\User;
use App\Services\DailySummary;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

#[Signature('saout:send-recap {--force : Ignore the local-hour check and send now}')]
#[Description('Send the daily recap email to users at their chosen local hour')]
class SendDailyRecap extends Command
{
    public function handle(DailySummary $summary): int
    {
        $sent = 0;

        User::query()
            ->whereNotNull('email_verified_at')
            ->whereHas('notificationSetting', fn ($q) => $q->where('email_recap', true))
            ->with(['notificationSetting', 'stats', 'streaks', 'quranProgress'])
            ->chunkById(200, function ($users) use ($summary, &$sent) {
                foreach ($users as $user) {
                    $tz = $user->timezone ?: config('app.timezone', 'UTC');
                    $now = Carbon::now($tz);

                    if (! $this->option('force') && $now->hour !== (int) $user->notificationSetting->recap_hour) {
                        continue;
                    }

                    // One recap per user per local day.
                    $key = "recap:{$user->id}:{$now->toDateString()}";
                    if (! $this->option('force') && ! Cache::add($key, true, now()->addDay())) {
                        continue;
                    }

                    Mail::to($user)->queue(new DailyRecap($user, $summary->for($user, $now)));
                    $sent++;
                }
            });

        $this->info("Queued {$sent} daily recap email(s).");

        return self::SUCCESS;
    }
}
