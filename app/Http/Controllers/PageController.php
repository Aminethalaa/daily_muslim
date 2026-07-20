<?php

namespace App\Http\Controllers;

use App\Services\PrayerTimes;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function azkar(Request $request)
    {
        $user = $request->user();
        $tz = $user->timezone ?: config('app.timezone', 'UTC');
        $today = \Carbon\Carbon::now($tz)->toDateString();

        $categories = \App\Models\AzkarCategory::withCount('items')->orderBy('sort')->get();
        $doneKeys = $user->azkarLogs()
            ->whereDate('date', $today)
            ->whereNotNull('completed_at')
            ->pluck('category_id')
            ->all();

        return view('pages.azkar', [
            'categories' => $categories,
            'doneIds' => $doneKeys,
        ]);
    }

    public function progress(Request $request)
    {
        $user = $request->user()->loadMissing('stats', 'streaks');

        return view('pages.progress', ['user' => $user]);
    }

    public function account(Request $request)
    {
        return view('pages.account', [
            'user' => $request->user(),
            'methods' => PrayerTimes::METHODS,
        ]);
    }

    public function updatePrayer(Request $request)
    {
        $data = $request->validate([
            'prayer_method' => ['required', 'string', 'in:'.implode(',', array_keys(PrayerTimes::METHODS))],
            'asr_madhhab' => ['required', 'in:Standard,Hanafi'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:64'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string', 'max:64'],
        ]);

        $request->user()->update($data);

        return back()->with('status', __('common.done'));
    }
}
