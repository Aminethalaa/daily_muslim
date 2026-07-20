<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Gamification;
use App\Services\HijriDate;
use App\Services\PrayerTimes;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Fard prayers in order.
    private const PRAYERS = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

    public function index(Request $request, PrayerTimes $prayerTimes, HijriDate $hijri, Gamification $game)
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('stats', 'streaks', 'quranProgress');

        $tz = $user->timezone ?: config('app.timezone', 'UTC');
        $now = Carbon::now($tz);
        $today = $now->toDateString();

        // Location — fall back to Makkah until the user sets their own.
        $lat = $user->latitude ?? 21.4225;
        $lng = $user->longitude ?? 39.8262;
        $tzHours = $now->utcOffset() / 60;

        $times = $prayerTimes->times(
            $now, $lat, $lng, $tzHours,
            $user->prayer_method ?: 'MWL',
            $user->asr_madhhab ?: 'Standard',
        );

        // Today's prayer statuses.
        $logged = $user->prayerLogs()->whereDate('date', $today)->pluck('status', 'prayer');

        $prayers = [];
        foreach (self::PRAYERS as $key) {
            $prayers[] = [
                'key' => $key,
                'time' => $times[$key],
                'carbon' => Carbon::parse("$today {$times[$key]}", $tz),
                'status' => $logged[$key] ?? null,
                'done' => isset($logged[$key]),
            ];
        }

        // Next upcoming prayer (else Fajr tomorrow).
        $next = collect($prayers)->firstWhere(fn ($p) => $p['carbon']->isFuture());
        if (! $next) {
            $next = [
                'key' => 'fajr',
                'time' => $times['fajr'],
                'carbon' => Carbon::parse($now->copy()->addDay()->toDateString()." {$times['fajr']}", $tz),
            ];
        }

        // Worship checklist rollups.
        $quran = $user->quranProgress;
        $pagesToday = (float) $user->quranSessions()->whereDate('date', $today)->sum('pages');
        $goalPages = $quran?->daily_goal_pages ?: 4;

        $azkarDone = $user->azkarLogs()
            ->whereDate('date', $today)
            ->whereColumn('completed_count', '>=', 'total')
            ->where('total', '>', 0)
            ->count();

        $sadakaDone = $user->sadakaLogs()->whereDate('date', $today)->exists();

        $prayersDone = collect($prayers)->where('done', true)->count();

        // Gamification progress.
        $progress = $game->progress((int) ($user->stats?->xp ?? 0));

        return view('dashboard', [
            'user' => $user,
            'now' => $now,
            'hijriDate' => $hijri->format($now, $user->locale, (int) $user->hijri_offset),
            'gregDate' => $now->translatedFormat('l، j F Y'),
            'times' => $times,
            'prayers' => $prayers,
            'next' => $next,
            'prayersDone' => $prayersDone,
            'pagesToday' => $pagesToday,
            'goalPages' => $goalPages,
            'azkarDone' => $azkarDone,
            'sadakaDone' => $sadakaDone,
            'progress' => $progress,
            'hasLocation' => $user->hasLocation(),
        ]);
    }
}
