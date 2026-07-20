<?php

namespace App\Http\Controllers;

use App\Services\Badges;
use App\Services\Gamification;
use App\Services\Streaks;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SadakaController extends Controller
{
    public const CATEGORIES = [
        'mosque' => 'مسجد',
        'poor' => 'فقراء ومساكين',
        'family' => 'الأهل والأقارب',
        'water' => 'سقيا الماء',
        'orphan' => 'الأيتام',
        'general' => 'صدقة عامة',
        'other' => 'أخرى',
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $tz = $user->timezone ?: config('app.timezone', 'UTC');
        $now = Carbon::now($tz);
        $monthStart = $now->copy()->startOfMonth()->toDateString();

        $monthLogs = $user->sadakaLogs()->whereDate('date', '>=', $monthStart)->get();
        $monthTotal = (float) $monthLogs->sum('amount');
        $monthCount = $monthLogs->count();

        $byCategory = $monthLogs->groupBy('category')->map(fn ($g) => [
            'count' => $g->count(),
            'total' => (float) $g->sum('amount'),
        ])->sortByDesc('total');

        $goal = $user->sadakaGoals()->where('active', true)->where('period', 'monthly')->first();

        return view('sadaka.index', [
            'categories' => self::CATEGORIES,
            'monthTotal' => $monthTotal,
            'monthCount' => $monthCount,
            'byCategory' => $byCategory,
            'goal' => $goal,
            'goalPercent' => $goal && $goal->target_amount > 0 ? min(100, (int) round($monthTotal / $goal->target_amount * 100)) : 0,
            'recent' => $user->sadakaLogs()->latest('date')->latest('id')->limit(12)->get(),
            'todayDone' => $user->sadakaLogs()->whereDate('date', $now->toDateString())->exists(),
        ]);
    }

    public function store(Request $request, Gamification $game, Streaks $streaks, Badges $badges)
    {
        $data = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(self::CATEGORIES))],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $tz = $user->timezone ?: config('app.timezone', 'UTC');
        $now = Carbon::now($tz);

        $user->sadakaLogs()->create([
            'date' => $now->toDateString(),
            'amount' => $data['amount'] ?? null,
            'category' => $data['category'],
            'note' => $data['note'] ?? null,
        ]);

        $game->award($user, 'sadaka', 8, 'sadaka:'.$now->toDateString().':'.$now->timestamp, $now);
        $streaks->touch($user, 'sadaka', $now);
        $streaks->touch($user, 'overall', $now);
        $badges->evaluate($user);

        return back()->with('status', __('common.done'));
    }

    public function setGoal(Request $request)
    {
        $data = $request->validate([
            'target_amount' => ['required', 'numeric', 'min:1', 'max:100000000'],
            'currency' => ['nullable', 'string', 'max:3'],
        ]);

        $user = $request->user();
        $user->sadakaGoals()->where('period', 'monthly')->update(['active' => false]);
        $user->sadakaGoals()->create([
            'period' => 'monthly',
            'target_amount' => $data['target_amount'],
            'currency' => $data['currency'] ?? 'USD',
            'active' => true,
        ]);

        return back()->with('status', __('common.done'));
    }

    public function destroy(Request $request, int $log)
    {
        $request->user()->sadakaLogs()->whereKey($log)->delete();

        return back();
    }
}
