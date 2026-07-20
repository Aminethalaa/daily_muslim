<x-layouts.app :title="__('dashboard.title')">
    @php($hour = (int) $now->format('H'))
    {{-- Greeting --}}
    <div class="mb-5">
        <p class="text-sm text-ink-900/60 dark:text-sand-100/60">
            {{ $hour < 17 ? __('common.greeting_morning') : __('common.greeting_evening') }}
        </p>
        <h1 class="font-display text-2xl font-bold text-primary-800 dark:text-sand-100">
            السلام عليكم، {{ $user->name }}
        </h1>
        <p class="mt-1 text-sm text-gold-600 dark:text-gold-400">{{ $hijriDate }}</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-2xl bg-primary-600/10 px-4 py-3 text-sm text-primary-700 dark:text-primary-300">{{ session('status') }}</div>
    @endif

    {{-- Next prayer hero --}}
    <div class="relative mb-5 overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 to-primary-900 p-6 text-white shadow-lg shadow-primary-900/20">
        <div class="absolute inset-0 opacity-10" style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20 0l20 20-20 20L0 20z' fill='none' stroke='%23fff' stroke-width='1'/%3E%3C/svg%3E&quot;)"></div>
        <div class="relative">
            <p class="text-sm text-gold-300">{{ __('prayer.next_prayer') }}</p>
            <div class="mt-1 flex items-end justify-between">
                <div>
                    <p class="font-display text-3xl font-bold">{{ __('prayer.'.$next['key']) }}</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-gold-200" dir="ltr">{{ $next['time'] }}</p>
                </div>
                <div class="text-end">
                    <p class="text-xs text-white/60">{{ __('prayer.time_remaining') }}</p>
                    <p class="font-mono text-xl font-bold tabular-nums" id="countdown" data-target="{{ $next['carbon']->toIso8601String() }}">—:—</p>
                </div>
            </div>
        </div>
    </div>

    @unless ($hasLocation)
        <a href="{{ route('account') }}" class="mb-5 flex items-center gap-3 rounded-2xl border border-gold-400/40 bg-gold-500/10 px-4 py-3 text-sm text-gold-700 dark:text-gold-300">
            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-6-7-11a7 7 0 0 1 14 0c0 5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
            حدّد موقعك لحساب أوقات الصلاة بدقّة
        </a>
    @endunless

    {{-- Today's prayers --}}
    <h2 class="section-title mb-3">{{ __('prayer.todays_prayers') }}</h2>
    <div class="mb-6 grid grid-cols-5 gap-2">
        @foreach ($prayers as $p)
            <form method="POST" action="{{ route('track.prayer', $p['key']) }}">
                @csrf
                <button type="submit" class="flex w-full flex-col items-center gap-1.5 rounded-2xl border p-2.5 transition active:scale-95
                    {{ $p['done']
                        ? 'border-primary-500 bg-primary-600 text-white'
                        : 'border-sand-200 bg-white text-ink-900/70 dark:border-white/10 dark:bg-ink-800 dark:text-sand-100/70' }}">
                    <span class="text-[11px] font-semibold">{{ __('prayer.'.$p['key']) }}</span>
                    <span class="text-[11px] tabular-nums opacity-70" dir="ltr">{{ $p['time'] }}</span>
                    <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $p['done'] ? 'bg-white/20' : 'bg-sand-100 dark:bg-white/5' }}">
                        @if ($p['done'])
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                        @else
                            <span class="h-2 w-2 rounded-full bg-primary-300"></span>
                        @endif
                    </span>
                </button>
            </form>
        @endforeach
    </div>

    {{-- Today's worship checklist --}}
    <h2 class="section-title mb-3">{{ __('dashboard.todays_worship') }}</h2>
    <div class="mb-6 space-y-3">
        {{-- Quran --}}
        <a href="{{ route('quran') }}" class="card flex items-center gap-4 p-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-600/10 text-primary-600 dark:text-primary-300">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 6c-1.5-1-4-1.5-6-1.5S3 5 3 5v13s1-.5 3-.5 4.5.5 6 1.5c1.5-1 4-1.5 6-1.5s3 .5 3 .5V5s-1-.5-3-.5-4.5.5-6 1.5z"/><path d="M12 6v13"/></svg>
            </span>
            <div class="flex-1">
                <p class="font-semibold text-ink-900 dark:text-sand-100">{{ __('dashboard.quran_goal') }}</p>
                <p class="text-xs text-ink-900/50 dark:text-sand-100/50">{{ (int) $pagesToday }} {{ __('dashboard.of') }} {{ $goalPages }} {{ __('dashboard.pages_read') }}</p>
                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-sand-200 dark:bg-white/10">
                    <div class="h-full rounded-full bg-primary-500" style="width: {{ min(100, $goalPages ? round($pagesToday / $goalPages * 100) : 0) }}%"></div>
                </div>
            </div>
        </a>

        {{-- Azkar --}}
        <a href="{{ route('azkar') }}" class="card flex items-center gap-4 p-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gold-500/15 text-gold-600 dark:text-gold-300">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/></svg>
            </span>
            <div class="flex-1">
                <p class="font-semibold text-ink-900 dark:text-sand-100">{{ __('nav.azkar') }}</p>
                <p class="text-xs text-ink-900/50 dark:text-sand-100/50">{{ __('dashboard.azkar_morning') }} · {{ __('dashboard.azkar_evening') }}</p>
            </div>
            <span class="chip {{ $azkarDone > 0 ? 'bg-primary-600/10 text-primary-700 dark:text-primary-300' : 'bg-sand-100 text-ink-900/50 dark:bg-white/5 dark:text-sand-100/50' }}">{{ $azkarDone }}/2</span>
        </a>

        {{-- Sadaka --}}
        <a href="{{ route('sadaka') }}" class="card flex items-center gap-4 p-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-600/10 text-primary-600 dark:text-primary-300">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg>
            </span>
            <div class="flex-1">
                <p class="font-semibold text-ink-900 dark:text-sand-100">{{ __('dashboard.sadaka') }}</p>
                <p class="text-xs text-ink-900/50 dark:text-sand-100/50">{{ __('dashboard.log_sadaka') }}</p>
            </div>
            @if ($sadakaDone)
                <span class="chip bg-primary-600 text-white">{{ __('common.done') }} ✓</span>
            @else
                <span class="chip bg-primary-600/10 text-primary-700 dark:text-primary-300">+ سجّل</span>
            @endif
        </a>
    </div>

    {{-- Level progress --}}
    <div class="card p-5">
        <div class="mb-2 flex items-center justify-between">
            <span class="flex items-center gap-2 font-semibold text-ink-900 dark:text-sand-100">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-gold-500 text-ink-900 text-sm font-bold">{{ $progress['level'] }}</span>
                {{ __('common.level') }} {{ $progress['level'] }}
            </span>
            <span class="text-xs text-ink-900/50 dark:text-sand-100/50">{{ $progress['to_next'] }} {{ __('dashboard.xp_to_next') }}</span>
        </div>
        <div class="h-2.5 overflow-hidden rounded-full bg-sand-200 dark:bg-white/10">
            <div class="h-full rounded-full bg-gradient-to-r from-gold-400 to-gold-600" style="width: {{ $progress['percent'] }}%"></div>
        </div>
    </div>

    <script>
        (function () {
            const el = document.getElementById('countdown');
            if (!el) return;
            const target = new Date(el.dataset.target).getTime();
            function tick() {
                let diff = Math.max(0, target - Date.now());
                const h = Math.floor(diff / 3.6e6);
                const m = Math.floor((diff % 3.6e6) / 6e4);
                const s = Math.floor((diff % 6e4) / 1e3);
                el.textContent = [h, m, s].map(n => String(n).padStart(2, '0')).join(':');
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
</x-layouts.app>
