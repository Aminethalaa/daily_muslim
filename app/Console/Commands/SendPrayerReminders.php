<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PrayerTimes;
use App\Services\WebPushSender;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

#[Signature('saout:send-prayer-reminders')]
#[Description('Send a web-push reminder to users whose prayer time is now')]
class SendPrayerReminders extends Command
{
    private const NAMES = [
        'fajr' => 'الفجر', 'dhuhr' => 'الظهر', 'asr' => 'العصر',
        'maghrib' => 'المغرب', 'isha' => 'العشاء',
    ];

    public function handle(PrayerTimes $prayerTimes, WebPushSender $push): int
    {
        if (! $push->configured()) {
            $this->warn('VAPID keys not configured; skipping.');

            return self::SUCCESS;
        }

        $sent = 0;

        User::query()
            ->whereHas('notificationSetting', fn ($q) => $q->where('prayer_reminders', true))
            ->whereHas('pushSubscriptions')
            ->with('notificationSetting')
            ->chunkById(200, function ($users) use ($prayerTimes, $push, &$sent) {
                foreach ($users as $user) {
                    $tz = $user->timezone ?: config('app.timezone', 'UTC');
                    $now = Carbon::now($tz);
                    $lead = (int) ($user->notificationSetting->prayer_lead_minutes ?? 0);

                    $times = $prayerTimes->times(
                        $now,
                        $user->latitude ?? 21.4225,
                        $user->longitude ?? 39.8262,
                        $now->utcOffset() / 60,
                        $user->prayer_method ?: 'MWL',
                        $user->asr_madhhab ?: 'Standard',
                    );

                    foreach (self::NAMES as $key => $label) {
                        $at = Carbon::parse($now->toDateString().' '.$times[$key], $tz)->subMinutes($lead);
                        if ($at->format('H:i') !== $now->format('H:i')) {
                            continue;
                        }

                        $guard = "prayer-push:{$user->id}:{$key}:{$now->toDateString()}";
                        if (! Cache::add($guard, true, now()->addHours(6))) {
                            continue;
                        }

                        $sent += $push->send($user, [
                            'title' => 'حان وقت صلاة '.$label,
                            'body' => 'حيَّ على الصلاة — سجّل صلاتك في صوت.',
                            'url' => '/dashboard',
                        ]);
                    }
                }
            });

        $this->info("Sent {$sent} prayer push notification(s).");

        return self::SUCCESS;
    }
}
