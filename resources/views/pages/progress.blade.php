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

    {{-- Badges --}}
    <div class="mb-3 flex items-center justify-between">
        <h2 class="section-title">الأوسمة</h2>
        <span class="text-xs text-ink-900/50 dark:text-sand-100/50">{{ $earned->count() }} / {{ $catalog->count() }}</span>
    </div>
    <div class="grid grid-cols-3 gap-3">
        @foreach ($catalog as $badge)
            @php($has = $earned->has($badge->id))
            @php($tierColor = ['bronze' => 'from-amber-600 to-amber-800', 'silver' => 'from-slate-400 to-slate-600', 'gold' => 'from-gold-400 to-gold-600'][$badge->tier] ?? 'from-primary-500 to-primary-700')
            <div class="card flex flex-col items-center gap-2 p-3 text-center {{ $has ? '' : 'opacity-45' }}">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br {{ $has ? $tierColor : 'from-sand-200 to-sand-300 dark:from-white/10 dark:to-white/5' }} text-white shadow-sm">
                    @if ($has)
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="currentColor"><path d="M12 2l2.4 6.9H22l-6 4.4 2.3 7-6.3-4.5L5.7 20l2.3-7-6-4.4h7.6z"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" class="h-6 w-6 text-ink-900/40 dark:text-sand-100/40" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                    @endif
                </span>
                <p class="text-xs font-semibold text-ink-900 dark:text-sand-100">{{ $badge->name_ar }}</p>
                <p class="text-[10px] leading-tight text-ink-900/45 dark:text-sand-100/45">{{ $badge->description_ar }}</p>
            </div>
        @endforeach
    </div>
</x-layouts.app>
