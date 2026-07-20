<?php

namespace App\Services;

use App\Models\User;

/**
 * Creates the satellite records every user needs (stats, settings, streaks,
 * quran progress). Idempotent — safe to call on login as well as registration.
 */
class UserProvisioner
{
    public function provision(User $user): User
    {
        $user->stats()->firstOrCreate([], [
            'points' => 0, 'xp' => 0, 'level' => 1,
        ]);

        $user->notificationSetting()->firstOrCreate([]);

        $user->quranProgress()->firstOrCreate([]);

        foreach (['salat', 'quran', 'azkar', 'sadaka', 'overall'] as $type) {
            $user->streaks()->firstOrCreate(['type' => $type]);
        }

        return $user->load('stats', 'streaks', 'notificationSetting', 'quranProgress');
    }
}
