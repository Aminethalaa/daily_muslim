<x-layouts.app :title="$meta['name']">
    @php($ar = fn ($n) => strtr((string) $n, ['0'=>'٠','1'=>'١','2'=>'٢','3'=>'٣','4'=>'٤','5'=>'٥','6'=>'٦','7'=>'٧','8'=>'٨','9'=>'٩']))

    <div x-data="quranReader({{ $meta['id'] }}, {{ json_encode($bookmarks) }})" x-init="init()">
        {{-- Sticky header --}}
        <div class="sticky top-[57px] z-20 -mx-4 mb-4 flex items-center justify-between border-b border-black/5 bg-sand-50/90 px-4 py-2.5 backdrop-blur dark:border-white/5 dark:bg-ink-900/90">
            <a href="{{ route('quran') }}" class="flex h-9 w-9 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 dark:bg-white/5 dark:text-sand-100">
                <svg viewBox="0 0 24 24" class="h-5 w-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div class="text-center">
                <p class="font-quran text-lg leading-none text-primary-800 dark:text-sand-100">{{ $meta['name'] }}</p>
                <p class="text-[10px] text-ink-900/45 dark:text-sand-100/45">{{ $meta['transliteration'] }} · {{ $ar($meta['total_verses']) }} آية</p>
            </div>
            <div class="flex items-center gap-1">
                <button @click="fontSize = Math.max(16, fontSize - 2)" class="flex h-9 w-8 items-center justify-center rounded-xl bg-primary-50 text-xs font-bold text-primary-700 dark:bg-white/5 dark:text-sand-100">أ−</button>
                <button @click="fontSize = Math.min(44, fontSize + 2)" class="flex h-9 w-8 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-700 dark:bg-white/5 dark:text-sand-100">أ+</button>
            </div>
        </div>

        {{-- Audio bar --}}
        <div class="mb-4 flex items-center gap-3 rounded-2xl bg-white p-3 shadow-sm ring-1 ring-black/5 dark:bg-ink-800 dark:ring-white/5">
            <button @click="toggleAudio()" class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 text-white">
                <svg x-show="!playing" viewBox="0 0 24 24" class="h-5 w-5 ps-0.5" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                <svg x-show="playing" x-cloak viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
            </button>
            <div class="flex-1">
                <p class="text-sm font-semibold text-ink-900 dark:text-sand-100">الاستماع للتلاوة</p>
                <p class="text-xs text-ink-900/45 dark:text-sand-100/45">مشاري العفاسي</p>
            </div>
            <audio x-ref="audio" src="{{ $audioUrl }}" preload="none" @play="playing=true" @pause="playing=false" @ended="playing=false"></audio>
        </div>

        {{-- Bismillah (except Al-Fatihah which includes it, and At-Tawbah which has none) --}}
        @if ($meta['id'] !== 1 && $meta['id'] !== 9)
            <p class="mb-5 text-center font-quran text-2xl text-primary-800 dark:text-sand-100">بِسۡمِ ٱللَّهِ ٱلرَّحۡمَٰنِ ٱلرَّحِيمِ</p>
        @endif

        {{-- Verses --}}
        <div class="card p-5">
            <p class="font-quran leading-loose text-ink-900 dark:text-sand-100" :style="`font-size:${fontSize}px; line-height:2.6`" dir="rtl">
                @foreach ($verses as $v)
                    <span class="quran-ayah" role="button"
                          @click="toggleBookmark({{ $v['id'] }}, $el)"
                          :class="bookmarks.includes({{ $v['id'] }}) ? 'bg-gold-500/15 rounded-lg' : ''">
                        {{ $v['text'] }}<span class="mx-1 inline-flex items-center justify-center align-middle font-sans text-[0.55em] font-bold text-primary-600 dark:text-gold-400">۝{{ $ar($v['id']) }}</span>
                    </span>
                @endforeach
            </p>
        </div>

        {{-- Surah navigation --}}
        <div class="mt-4 grid grid-cols-2 gap-3">
            @if ($prev)
                <a href="{{ route('quran.show', $prev['id']) }}" class="btn-ghost">‹ {{ $prev['name'] }}</a>
            @else
                <span></span>
            @endif
            @if ($next)
                <a href="{{ route('quran.show', $next['id']) }}" class="btn-ghost">{{ $next['name'] }} ›</a>
            @endif
        </div>
    </div>

    {{-- Floating "log reading" --}}
    <form method="POST" action="{{ route('quran.log') }}" class="fixed inset-x-0 bottom-20 z-30 mx-auto flex max-w-md justify-center px-4">
        @csrf
        <input type="hidden" name="surah" value="{{ $meta['id'] }}">
        <input type="hidden" name="last_ayah" value="{{ $meta['total_verses'] }}">
        <button type="submit" class="btn-gold shadow-lg shadow-gold-600/20">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 13l4 4L19 7"/></svg>
            سجّل ورد اليوم
        </button>
    </form>

    <script>
        function quranReader(surah, initialBookmarks) {
            return {
                surah,
                bookmarks: initialBookmarks || [],
                playing: false,
                fontSize: 26,
                init() {
                    const saved = parseInt(localStorage.getItem('saout-quran-fs') || '26');
                    this.fontSize = saved;
                    this.$watch('fontSize', (v) => localStorage.setItem('saout-quran-fs', v));
                },
                toggleAudio() {
                    const a = this.$refs.audio;
                    if (a.paused) a.play().catch(() => {}); else a.pause();
                },
                async toggleBookmark(ayah) {
                    const i = this.bookmarks.indexOf(ayah);
                    if (i >= 0) this.bookmarks.splice(i, 1); else this.bookmarks.push(ayah);
                    try {
                        await fetch('{{ route('quran.bookmark') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            },
                            body: JSON.stringify({ surah: this.surah, ayah }),
                        });
                    } catch (e) {}
                },
            };
        }
    </script>
</x-layouts.app>
