<x-layouts.app :title="__('nav.azkar')">
    <h1 class="mb-4 font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('nav.azkar') }}</h1>

    <div class="grid grid-cols-2 gap-3">
        @foreach ([
            ['key' => 'morning', 'name' => 'أذكار الصباح', 'icon' => 'sun'],
            ['key' => 'evening', 'name' => 'أذكار المساء', 'icon' => 'moon'],
            ['key' => 'after_salah', 'name' => 'أذكار بعد الصلاة', 'icon' => 'star'],
            ['key' => 'sleep', 'name' => 'أذكار النوم', 'icon' => 'moon'],
        ] as $cat)
            <div class="card flex flex-col items-center gap-2 p-5 text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gold-500/15 text-gold-600 dark:text-gold-300">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor"><path d="M12 2l2.4 6.9H22l-6 4.4 2.3 7-6.3-4.5L5.7 20l2.3-7-6-4.4h7.6z"/></svg>
                </span>
                <p class="font-semibold text-ink-900 dark:text-sand-100">{{ $cat['name'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-5 rounded-3xl border border-dashed border-gold-400/50 bg-gold-500/5 p-6 text-center">
        <p class="font-semibold text-ink-900 dark:text-sand-100">مكتبة الأذكار مع العدّادات قريباً</p>
        <p class="mt-1 text-sm text-ink-900/60 dark:text-sand-100/60">أذكار حصن المسلم كاملة مع عدّاد لكل ذكر وتتبّع الإتمام — في المرحلة القادمة بإذن الله.</p>
    </div>
</x-layouts.app>
