<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Evaluates achievement criteria and awards newly earned badges. Idempotent —
 * safe to call after any action or on page load.
 */
class Badges
{
    /**
     * @return Collection<int,Badge> newly earned badges
     */
    public function evaluate(User $user): Collection
    {
        $user->loadMissing('stats', 'streaks', 'quranProgress');
        $streaks = $user->streaks->keyBy('type');

        $met = [
            'first_step' => $user->xpEvents()->exists(),
            'week_warrior' => ($streaks->get('overall')?->current ?? 0) >= 7,
            'forty_days' => ($streaks->get('overall')?->current ?? 0) >= 40,
            'hundred_days' => ($streaks->get('overall')?->current ?? 0) >= 100,
            'salat_keeper' => ($streaks->get('salat')?->current ?? 0) >= 7,
            'quran_companion' => ($streaks->get('quran')?->current ?? 0) >= 7,
            'first_khatma' => ($user->quranProgress?->khatma_count ?? 0) >= 1,
            'azkar_devotee' => ($streaks->get('azkar')?->current ?? 0) >= 7,
            'generous_soul' => $user->sadakaLogs()->count() >= 10,
            'rising_star' => ($user->stats?->level ?? 1) >= 5,
        ];

        $metKeys = array_keys(array_filter($met));
        if (empty($metKeys)) {
            return collect();
        }

        $earnedKeys = $user->badges()->pluck('key')->all();
        $toAward = array_diff($metKeys, $earnedKeys);
        if (empty($toAward)) {
            return collect();
        }

        $badges = Badge::whereIn('key', $toAward)->get();
        foreach ($badges as $badge) {
            $user->badges()->attach($badge->id, ['earned_at' => now()]);
        }

        return $badges;
    }
}
