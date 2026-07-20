<x-layouts.app :title="__('dashboard.sadaka')">
    @php($cur = $goal->currency ?? '')
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="flex h-9 w-9 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 dark:bg-white/5 dark:text-sand-100">
            <svg viewBox="0 0 24 24" class="h-5 w-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <h1 class="font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('dashboard.sadaka') }}</h1>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-2xl bg-primary-600/10 px-4 py-3 text-sm text-primary-700 dark:text-primary-300">{{ session('status') }}</div>
    @endif

    {{-- This month total + goal --}}
    <div class="card mb-5 p-5">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs text-ink-900/50 dark:text-sand-100/50">صدقات هذا الشهر</p>
                <p class="text-3xl font-bold text-primary-700 dark:text-primary-300">{{ number_format($monthTotal, $monthTotal == (int) $monthTotal ? 0 : 2) }} <span class="text-sm font-normal">{{ $cur }}</span></p>
            </div>
            <span class="chip bg-primary-600/10 text-primary-700 dark:text-primary-300">{{ $monthCount }} صدقة</span>
        </div>

        @if ($goal)
            <div class="mt-4">
                <div class="mb-1 flex justify-between text-xs text-ink-900/50 dark:text-sand-100/50">
                    <span>الهدف الشهري: {{ number_format($goal->target_amount, 0) }} {{ $cur }}</span>
                    <span>{{ $goalPercent }}%</span>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-sand-200 dark:bg-white/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-gold-400 to-gold-600" style="width: {{ $goalPercent }}%"></div>
                </div>
            </div>
        @endif
        <p class="mt-3 text-[11px] text-ink-900/40 dark:text-sand-100/40">🔒 مبالغك خاصة بك، ولا تظهر لأحد.</p>
    </div>

    {{-- Log a sadaka --}}
    <h2 class="section-title mb-3">سجّل صدقة</h2>
    <form method="POST" action="{{ route('sadaka.store') }}" class="card mb-5 space-y-4 p-5" x-data="{ cat: 'general' }">
        @csrf
        <div>
            <label class="label">النوع</label>
            <div class="flex flex-wrap gap-2">
                @foreach ($categories as $key => $label)
                    <button type="button" @click="cat = '{{ $key }}'"
                            class="chip transition"
                            :class="cat === '{{ $key }}' ? 'bg-primary-600 text-white' : 'bg-sand-100 text-ink-900/70 dark:bg-white/5 dark:text-sand-100/70'">{{ $label }}</button>
                @endforeach
            </div>
            <input type="hidden" name="category" :value="cat">
        </div>

        <div>
            <label class="label" for="amount">المبلغ <span class="text-ink-900/40">(اختياري)</span></label>
            <input id="amount" name="amount" type="number" step="any" min="0" inputmode="decimal" class="field" dir="ltr" placeholder="0">
        </div>

        <div>
            <label class="label" for="note">ملاحظة <span class="text-ink-900/40">(اختياري)</span></label>
            <input id="note" name="note" type="text" maxlength="255" class="field" placeholder="مثال: كفالة يتيم">
        </div>

        <button type="submit" class="btn-primary w-full">سجّل الصدقة</button>
    </form>

    {{-- Monthly goal setter --}}
    <details class="card mb-5 p-5" @if(!$goal) open @endif>
        <summary class="cursor-pointer font-semibold text-ink-900 dark:text-sand-100">{{ $goal ? 'تعديل الهدف الشهري' : 'حدّد هدفاً شهرياً' }}</summary>
        <form method="POST" action="{{ route('sadaka.goal') }}" class="mt-4 flex gap-2">
            @csrf
            <input name="target_amount" type="number" step="any" min="1" required class="field" dir="ltr" placeholder="مثال: 500" value="{{ $goal->target_amount ?? '' }}">
            <input name="currency" type="text" maxlength="3" class="field w-24 text-center" dir="ltr" placeholder="USD" value="{{ $goal->currency ?? '' }}">
            <button type="submit" class="btn-gold shrink-0">{{ __('common.save') }}</button>
        </form>
    </details>

    {{-- Category breakdown --}}
    @if ($byCategory->isNotEmpty() && $monthTotal > 0)
        <h2 class="section-title mb-3">التوزيع حسب النوع</h2>
        <div class="card mb-5 space-y-3 p-5">
            @foreach ($byCategory as $key => $row)
                @if ($row['total'] > 0)
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-ink-900/60 dark:text-sand-100/60">
                            <span>{{ $categories[$key] ?? $key }}</span>
                            <span>{{ number_format($row['total'], 0) }} {{ $cur }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-sand-200 dark:bg-white/10">
                            <div class="h-full rounded-full bg-primary-500" style="width: {{ min(100, round($row['total'] / max($monthTotal, 1) * 100)) }}%"></div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Recent --}}
    @if ($recent->isNotEmpty())
        <h2 class="section-title mb-3">آخر الصدقات</h2>
        <div class="space-y-2">
            @foreach ($recent as $log)
                <div class="card flex items-center gap-3 p-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600/10 text-primary-600 dark:text-primary-300">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/></svg>
                    </span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-ink-900 dark:text-sand-100">{{ $categories[$log->category] ?? $log->category }}</p>
                        <p class="text-xs text-ink-900/45 dark:text-sand-100/45">{{ $log->date->translatedFormat('j F') }}{{ $log->note ? ' · '.$log->note : '' }}</p>
                    </div>
                    @if ($log->amount)
                        <span class="text-sm font-bold text-primary-700 dark:text-primary-300">{{ number_format($log->amount, $log->amount == (int) $log->amount ? 0 : 2) }}</span>
                    @endif
                    <form method="POST" action="{{ route('sadaka.destroy', $log->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex h-7 w-7 items-center justify-center rounded-lg text-ink-900/30 hover:text-red-500 dark:text-sand-100/30" aria-label="delete">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
