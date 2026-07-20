<x-layouts.app :title="__('nav.azkar')">
    <h1 class="mb-4 font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('nav.azkar') }}</h1>

    @if ($categories->isEmpty())
        <div class="rounded-3xl border border-dashed border-gold-400/50 bg-gold-500/5 p-6 text-center">
            <p class="text-sm text-ink-900/60 dark:text-sand-100/60">لم تُحمّل الأذكار بعد. شغّل الأمر: <code>php artisan db:seed</code></p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($categories as $cat)
                @php($done = in_array($cat->id, $doneIds, true))
                <a href="{{ route('azkar.session', $cat->key) }}" class="card flex items-center gap-4 p-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $done ? 'bg-primary-600 text-white' : 'bg-gold-500/15 text-gold-600 dark:text-gold-300' }}">
                        @if ($cat->icon === 'sun')
                            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19"/></svg>
                        @elseif ($cat->icon === 'moon')
                            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor"><path d="M12 2l2.4 6.9H22l-6 4.4 2.3 7-6.3-4.5L5.7 20l2.3-7-6-4.4h7.6z"/></svg>
                        @endif
                    </span>
                    <div class="flex-1">
                        <p class="font-semibold text-ink-900 dark:text-sand-100">{{ $cat->name_ar }}</p>
                        <p class="text-xs text-ink-900/50 dark:text-sand-100/50">{{ $cat->items_count }} أذكار</p>
                    </div>
                    @if ($done)
                        <span class="chip bg-primary-600 text-white">تمّت ✓</span>
                    @else
                        <svg viewBox="0 0 24 24" class="h-5 w-5 text-ink-900/30 rtl:rotate-180 dark:text-sand-100/30" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.app>
