<x-layouts.app :title="__('nav.quran')">
    @php($ar = fn ($n) => strtr((string) $n, ['0'=>'٠','1'=>'١','2'=>'٢','3'=>'٣','4'=>'٤','5'=>'٥','6'=>'٦','7'=>'٧','8'=>'٨','9'=>'٩']))
    <h1 class="mb-4 font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('nav.quran') }}</h1>

    {{-- Continue reading + daily goal --}}
    <a href="{{ route('quran.show', $progress->last_surah ?: 1) }}"
       class="mb-4 block overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 to-primary-900 p-5 text-white shadow-lg">
        <p class="text-xs text-gold-300">متابعة القراءة</p>
        <p class="mt-1 font-quran text-2xl">{{ $lastSurahMeta['name'] ?? 'الفاتحة' }}</p>
        <div class="mt-3 flex items-center justify-between text-xs">
            <span class="text-white/70">ورد اليوم: {{ (int) $pagesToday }} / {{ $goalPages }} صفحة · ختمات: {{ $ar($progress->khatma_count) }}</span>
            <span class="rounded-full bg-white/15 px-3 py-1">استئناف ‹</span>
        </div>
        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white/15">
            <div class="h-full rounded-full bg-gold-400" style="width: {{ min(100, $goalPages ? round($pagesToday / $goalPages * 100) : 0) }}%"></div>
        </div>
    </a>

    {{-- Search + list --}}
    <div x-data="{ q: '' }">
        <div class="relative mb-3">
            <input x-model="q" type="search" placeholder="ابحث عن سورة…" class="field ps-10">
            <svg viewBox="0 0 24 24" class="pointer-events-none absolute inset-y-0 start-3 my-auto h-5 w-5 text-ink-900/40 dark:text-sand-100/40" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
        </div>

        <div class="space-y-2">
            @foreach ($surahs as $s)
                <a href="{{ route('quran.show', $s['id']) }}"
                   x-show="q === '' || @js($s['name']).includes(q) || @js(strtolower($s['transliteration'])).includes(q.toLowerCase()) || @js((string) $s['id']) === q"
                   class="card flex items-center gap-3 p-3">
                    <span class="relative flex h-11 w-11 shrink-0 items-center justify-center">
                        <svg viewBox="0 0 24 24" class="absolute h-11 w-11 text-gold-500/40" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 2l2.6 2L18 3.5l.5 3.5 3 2-2 3 2 3-3 2L18 20.5 14.6 20 12 22l-2.6-2L6 20.5 5.5 17l-3-2 2-3-2-3 3-2L6 3.5 9.4 4z"/></svg>
                        <span class="relative text-sm font-bold text-primary-700 dark:text-primary-300">{{ $ar($s['id']) }}</span>
                    </span>
                    <div class="flex-1">
                        <p class="font-quran text-lg text-ink-900 dark:text-sand-100">{{ $s['name'] }}</p>
                        <p class="text-xs text-ink-900/45 dark:text-sand-100/45">{{ $s['transliteration'] }} · {{ $ar($s['total_verses']) }} آية</p>
                    </div>
                    <span class="chip {{ $s['type'] === 'meccan' ? 'bg-gold-500/15 text-gold-700 dark:text-gold-300' : 'bg-primary-600/10 text-primary-700 dark:text-primary-300' }}">
                        {{ $s['type'] === 'meccan' ? 'مكية' : 'مدنية' }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
