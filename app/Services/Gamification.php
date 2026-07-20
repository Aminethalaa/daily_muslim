<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Points / XP / levels engine. All awards are written to the xp_events ledger
 * (auditable, anti-gaming) and rolled up into user_stats.
 */
class Gamification
{
    /** Per-day cap per source, to prevent farming. */
    public const DAILY_CAPS = [
        'prayer' => 120,
        'quran' => 40,
        'azkar' => 30,
        'sadaka' => 20,
        'bonus' => 40,
    ];

    /** Cumulative XP required to *reach* a given level. */
    public function xpForLevel(int $level): int
    {
        $level = max(1, $level);

        return 50 * ($level - 1) * $level; // L1:0, L2:100, L3:300, L4:600 …
    }

    public function levelForXp(int $xp): int
    {
        $level = 1;
        while ($this->xpForLevel($level + 1) <= $xp) {
            $level++;
        }

        return $level;
    }

    /**
     * Progress toward the next level.
     *
     * @return array{level:int,into:int,span:int,percent:int}
     */
    public function progress(int $xp): array
    {
        $level = $this->levelForXp($xp);
        $base = $this->xpForLevel($level);
        $next = $this->xpForLevel($level + 1);
        $span = max(1, $next - $base);
        $into = $xp - $base;

        return [
            'level' => $level,
            'into' => $into,
            'span' => $span,
            'percent' => (int) round($into / $span * 100),
            'to_next' => $next - $xp,
        ];
    }

    /**
     * Award points for an act (idempotent per $ref). Respects daily caps.
     */
    public function award(User $user, string $source, int $points, string $ref, ?Carbon $date = null): void
    {
        $date ??= now();

        DB::transaction(function () use ($user, $source, $points, $ref, $date) {
            // Idempotency: never double-award the same act.
            if ($user->xpEvents()->where('ref', $ref)->exists()) {
                return;
            }

            // Enforce daily cap for this source.
            $spentToday = (int) $user->xpEvents()
                ->where('source', $source)
                ->whereDate('date', $date->toDateString())
                ->sum('points');

            $cap = self::DAILY_CAPS[$source] ?? 9999;
            $points = max(0, min($points, $cap - $spentToday));

            if ($points <= 0) {
                return;
            }

            $user->xpEvents()->create([
                'source' => $source,
                'points' => $points,
                'ref' => $ref,
                'date' => $date->toDateString(),
            ]);

            /** @var UserStat $stats */
            $stats = $user->stats()->firstOrCreate([]);
            $stats->increment('points', $points);
            $stats->increment('xp', $points);
            $stats->update(['level' => $this->levelForXp((int) $stats->xp)]);
        });
    }
}
