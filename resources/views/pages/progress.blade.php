<x-layouts.app :title="__('nav.progress')">
    <h1 class="mb-4 font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('nav.progress') }}</h1>

    {{-- Points & level --}}
    <div class="card mb-5 flex items-center justify-between p-5">
        <div>
            <p class="text-xs text-ink-900/50 dark:text-sand-100/50">{{ __('common.points') }}</p>
            <p class="text-3xl font-bold text-primary-700 dark:text-primary-300">{{ number_format($user->display_points) }}</p>
        </div>
        <div class="flex h-16 w-16 flex-col items-center justify-center rounded-2xl bg-gold-500 text-ink-900">
            <span class="text-2xl font-bold leading-none">{{ $user->level_number }}</span>
            <span class="text-[10px]">{{ __('common.level') }}</span>
        </div>
    </div>

    {{-- Streaks --}}
    <h2 class="section-title mb-3">سلاسل الأيام</h2>
    <div class="mb-6 grid grid-cols-2 gap-3">
        @php($labels = ['salat' => 'الصلاة', 'quran' => 'القرآن', 'azkar' => 'الأذكار', 'sadaka' => 'الصدقة', 'overall' => 'الإجمالي'])
        @foreach ($labels as $type => $label)
            @php($s = $user->streaks->firstWhere('type', $type))
            <div class="card flex items-center gap-3 p-4">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gold-500/15 text-gold-600 dark:text-gold-300">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor"><path d="M12 2s5 4.5 5 9a5 5 0 0 1-10 0c0-1.5.6-2.8 1.2-3.8C8.9 8.9 9 10 10 10c1.1 0 1-1.6.5-3.2C10 5 12 2 12 2z"/></svg>
                </span>
                <div>
                    <p class="text-2xl font-bold leading-none text-ink-900 dark:text-sand-100">{{ $s->current ?? 0 }}</p>
                    <p class="text-xs text-ink-900/50 dark:text-sand-100/50">{{ $label }} · أطول {{ $s->longest ?? 0 }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Badges placeholder --}}
    <h2 class="section-title mb-3">الأوسمة</h2>
    <div class="rounded-3xl border border-dashed border-primary-300 bg-primary-50/50 p-6 text-center dark:border-white/10 dark:bg-white/5">
        <p class="text-sm text-ink-900/60 dark:text-sand-100/60">ستحصل على أوسمة عند بلوغ الإنجازات — أسبوع كامل، ٤٠ يوماً، أول ختمة، وغيرها.</p>
    </div>
</x-layouts.app>
