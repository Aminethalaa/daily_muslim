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
        $user = $request->user();

        return view('pages.account', [
            'user' => $user,
            'methods' => PrayerTimes::METHODS,
            'notif' => $user->notificationSetting()->firstOrCreate([]),
            'pushEnabled' => $user->pushSubscriptions()->exists(),
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $data = $request->validate([
            'prayer_reminders' => ['nullable', 'boolean'],
            'azkar_reminders' => ['nullable', 'boolean'],
            'quran_reminder' => ['nullable', 'boolean'],
            'email_recap' => ['nullable', 'boolean'],
            'email_weekly' => ['nullable', 'boolean'],
            'recap_hour' => ['nullable', 'integer', 'between:0,23'],
        ]);

        $setting = $request->user()->notificationSetting()->firstOrCreate([]);
        $setting->update([
            'prayer_reminders' => $request->boolean('prayer_reminders'),
            'azkar_reminders' => $request->boolean('azkar_reminders'),
            'quran_reminder' => $request->boolean('quran_reminder'),
            'email_recap' => $request->boolean('email_recap'),
            'email_weekly' => $request->boolean('email_weekly'),
            'recap_hour' => $data['recap_hour'] ?? $setting->recap_hour,
        ]);

        return back()->with('status', __('common.done'));
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
