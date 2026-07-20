<div>
    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('azkar') }}" class="flex h-9 w-9 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 dark:bg-white/5 dark:text-sand-100">
            <svg viewBox="0 0 24 24" class="h-5 w-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <h1 class="font-display text-xl font-bold text-primary-800 dark:text-sand-100">{{ $category->name_ar }}</h1>
        <span class="chip bg-primary-600/10 text-primary-700 dark:text-primary-300">{{ count($completed) }}/{{ $items->count() }}</span>
    </div>

    {{-- Progress --}}
    <div class="mb-5 h-2 overflow-hidden rounded-full bg-sand-200 dark:bg-white/10">
        <div class="h-full rounded-full bg-primary-500 transition-all duration-500" style="width: {{ $this->progressPercent() }}%"></div>
    </div>

    @if ($finished)
        <div class="mb-5 rounded-3xl bg-gradient-to-br from-primary-600 to-primary-800 p-6 text-center text-white shadow-lg">
            <p class="text-3xl">✦</p>
            <p class="mt-2 font-display text-xl font-bold">تقبّل الله</p>
            <p class="mt-1 text-sm text-white/80">أتممت {{ $category->name_ar }} — تم منحك ٨ نقاط</p>
        </div>
    @endif

    {{-- Azkar list --}}
    <div class="space-y-3">
        @foreach ($items as $item)
            <div wire:key="dhikr-{{ $item->id }}"
                 x-data="{ n: {{ $counts[$item->id] ?? $item->repeat_count }}, total: {{ $item->repeat_count }} }"
                 class="card p-5 transition"
                 :class="n === 0 ? 'ring-2 ring-primary-500/40' : ''">

                <p class="font-quran text-xl leading-loose text-ink-900 dark:text-sand-100" style="line-height: 2.4">{{ $item->text_ar }}</p>

                @if ($item->reference)
                    <p class="mt-2 text-xs text-ink-900/45 dark:text-sand-100/45">{{ $item->reference }}</p>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    {{-- reset --}}
                    <button type="button"
                            @click="n = total; $wire.undo({{ $item->id }})"
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-sand-100 text-ink-900/50 dark:bg-white/5 dark:text-sand-100/50"
                            aria-label="reset">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                    </button>

                    {{-- big counter --}}
                    <button type="button"
                            @click="if (n > 0) { n--; if (n === 0) $wire.markComplete({{ $item->id }}) }"
                            class="flex h-16 w-16 items-center justify-center rounded-full text-2xl font-bold tabular-nums shadow-sm transition active:scale-90"
                            :class="n === 0 ? 'bg-primary-600 text-white' : 'bg-gold-500/15 text-gold-700 dark:text-gold-300'">
                        <template x-if="n > 0"><span x-text="n"></span></template>
                        <template x-if="n === 0">
                            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                        </template>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
