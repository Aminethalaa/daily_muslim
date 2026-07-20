<?php

namespace App\Services;

use App\Models\Streak;
use App\Models\User;
use Carbon\Carbon;

/**
 * Maintains per-habit daily streaks. A missed day consumes a "freeze" if one is
 * available (grace), otherwise the streak resets — encouraging, not punishing.
 */
class Streaks
{
    /**
     * Register activity for a habit on a given date and recompute the streak.
     */
    public function touch(User $user, string $type, ?Carbon $date = null): Streak
    {
        $date ??= now();
        $day = $date->copy()->startOfDay();

        /** @var Streak $streak */
        $streak = $user->streaks()->firstOrCreate(['type' => $type]);

        $last = $streak->last_date?->copy()->startOfDay();

        if ($last === null) {
            $streak->current = 1;
        } elseif ($last->equalTo($day)) {
            // Already counted today — nothing to change.
            return $streak;
        } elseif ($last->equalTo($day->copy()->subDay())) {
            $streak->current += 1;
        } else {
            $gap = $last->diffInDays($day) - 1;
            if ($gap > 0 && $streak->freezes >= $gap) {
                $streak->freezes -= $gap;   // spend grace days
                $streak->current += 1;
            } else {
                $streak->current = 1;       // streak broken
            }
        }

        $streak->last_date = $day->toDateString();
        $streak->longest = max($streak->longest, $streak->current);
        $streak->save();

        return $streak;
    }
}
