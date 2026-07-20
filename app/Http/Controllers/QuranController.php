<?php

namespace App\Http\Controllers;

use App\Services\Gamification;
use App\Services\Quran;
use App\Services\Streaks;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QuranController extends Controller
{
    public function __construct(private Quran $quran) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $tz = $user->timezone ?: config('app.timezone', 'UTC');
        $today = Carbon::now($tz)->toDateString();

        $progress = $user->quranProgress()->firstOrCreate([]);
        $pagesToday = (float) $user->quranSessions()->whereDate('date', $today)->sum('pages');

        return view('quran.index', [
            'surahs' => $this->quran->surahs(),
            'progress' => $progress,
            'lastSurahMeta' => $this->quran->surahMeta($progress->last_surah ?: 1),
            'pagesToday' => $pagesToday,
            'goalPages' => $progress->daily_goal_pages ?: 4,
        ]);
    }

    public function show(Request $request, int $surah)
    {
        $meta = $this->quran->surahMeta($surah);
        abort_if(! $meta, 404);

        $user = $request->user();
        $bookmarks = $user->quranBookmarks()->where('surah', $surah)->pluck('ayah')->all();

        return view('quran.show', [
            'meta' => $meta,
            'verses' => $this->quran->verses($surah),
            'prev' => $surah > 1 ? $this->quran->surahMeta($surah - 1) : null,
            'next' => $surah < 114 ? $this->quran->surahMeta($surah + 1) : null,
            'bookmarks' => $bookmarks,
            // Full-surah recitation (Mishary Alafasy) — streamed in production.
            'audioUrl' => sprintf('https://cdn.islamic.network/quran/audio-surah/128/ar.alafasy/%d.mp3', $surah),
        ]);
    }

    public function logReading(Request $request, Gamification $game, Streaks $streaks)
    {
        $data = $request->validate([
            'surah' => ['required', 'integer', 'between:1,114'],
            'last_ayah' => ['nullable', 'integer', 'min:1'],
            'pages' => ['nullable', 'numeric', 'min:0.5', 'max:60'],
        ]);

        $user = $request->user();
        $tz = $user->timezone ?: config('app.timezone', 'UTC');
        $now = Carbon::now($tz);
        $date = $now->toDateString();
        $pages = $data['pages'] ?? $this->quran->estimatePages($data['surah']);

        $user->quranSessions()->create([
            'date' => $date,
            'mode' => 'read',
            'from_surah' => $data['surah'],
            'to_surah' => $data['surah'],
            'to_ayah' => $data['last_ayah'] ?? null,
            'pages' => $pages,
        ]);

        $user->quranProgress()->firstOrCreate([])->update([
            'last_surah' => $data['surah'],
            'last_ayah' => $data['last_ayah'] ?? 1,
        ]);

        // XP scales a little with pages; capped daily by the Gamification service.
        $xp = (int) min(20, max(5, round($pages * 3)));
        $game->award($user, 'quran', $xp, "quran:$date:".$data['surah'].':'.$now->timestamp, $now);
        $streaks->touch($user, 'quran', $now);
        $streaks->touch($user, 'overall', $now);

        return back()->with('status', __('common.done'));
    }

    public function bookmark(Request $request)
    {
        $data = $request->validate([
            'surah' => ['required', 'integer', 'between:1,114'],
            'ayah' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();
        $existing = $user->quranBookmarks()->where($data)->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
        } else {
            $user->quranBookmarks()->create($data);
            $bookmarked = true;
        }

        return response()->json(['bookmarked' => $bookmarked]);
    }
}
