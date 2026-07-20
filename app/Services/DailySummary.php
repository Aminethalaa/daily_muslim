<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

/**
 * Computes a user's worship summary for a given day — used by the daily recap
 * email and the weekly summary.
 */
class DailySummary
{
    public function __construct(private HijriDate $hijri) {}

    public function for(User $user, ?Carbon $date = null): array
    {
        $tz = $user->timezone ?: config('app.timezone', 'UTC');
        $date ??= Carbon::now($tz);
        $day = $date->toDateString();

        $prayersDone = $user->prayerLogs()->whereDate('date', $day)
            ->whereIn('prayer', ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'])->count();

        $pages = (float) $user->quranSessions()->whereDate('date', $day)->sum('pages');
        $goalPages = $user->quranProgress?->daily_goal_pages ?: 4;

        $azkarDone = $user->azkarLogs()->whereDate('date', $day)
            ->whereNotNull('completed_at')
            ->whereHas('category', fn ($q) => $q->whereIn('key', ['morning', 'evening']))
            ->count();

        $sadakaDone = $user->sadakaLogs()->whereDate('date', $day)->exists();

        $xpToday = (int) $user->xpEvents()->whereDate('date', $day)->sum('points');

        $streaks = $user->streaks->keyBy('type');

        return [
            'date' => $date->copy(),
            'hijri' => $this->hijri->format($date, $user->locale, (int) $user->hijri_offset),
            'prayers_done' => $prayersDone,
            'prayers_total' => 5,
            'quran_pages' => (int) $pages,
            'quran_goal' => $goalPages,
            'azkar_done' => $azkarDone,
            'sadaka_done' => $sadakaDone,
            'xp_today' => $xpToday,
            'level' => (int) ($user->stats?->level ?? 1),
            'points' => (int) ($user->stats?->points ?? 0),
            'streak_salat' => (int) ($streaks->get('salat')?->current ?? 0),
            'streak_overall' => (int) ($streaks->get('overall')?->current ?? 0),
            'all_done' => $prayersDone >= 5 && $pages >= $goalPages && $azkarDone >= 2 && $sadakaDone,
        ];
    }
}
