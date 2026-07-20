<?php

namespace App\Http\Controllers;

use App\Services\Gamification;
use App\Services\Streaks;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    private const FARD = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

    /** XP per fard prayer (Fajr & Isha carry a small extra encouragement). */
    private const PRAYER_XP = ['fajr' => 13, 'dhuhr' => 10, 'asr' => 10, 'maghrib' => 10, 'isha' => 13];

    public function prayer(Request $request, string $prayer, Gamification $game, Streaks $streaks)
    {
        abort_unless(in_array($prayer, self::FARD, true), 404);

        $user = $request->user();
        $tz = $user->timezone ?: config('app.timezone', 'UTC');
        $today = Carbon::now($tz);
        $date = $today->toDateString();

        $existing = $user->prayerLogs()
            ->whereDate('date', $date)->where('prayer', $prayer)->first();

        if ($existing) {
            $existing->delete(); // un-mark (XP already recorded stays, award is idempotent per day)
        } else {
            $user->prayerLogs()->create([
                'date' => $date,
                'prayer' => $prayer,
                'status' => $request->input('status', 'on_time'),
                'logged_at' => now(),
            ]);

            $game->award($user, 'prayer', self::PRAYER_XP[$prayer] ?? 10, "prayer:$prayer:$date", $today);
            $streaks->touch($user, 'salat', $today);
            $streaks->touch($user, 'overall', $today);
            app(\App\Services\Badges::class)->evaluate($user);
        }

        return back();
    }
}
