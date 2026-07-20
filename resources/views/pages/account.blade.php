<x-layouts.app :title="__('nav.account')">
    <h1 class="mb-4 font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('nav.account') }}</h1>

    @if (session('status'))
        <div class="mb-4 rounded-2xl bg-primary-600/10 px-4 py-3 text-sm text-primary-700 dark:text-primary-300">{{ session('status') }}</div>
    @endif

    {{-- Profile --}}
    <div class="card mb-5 flex items-center gap-4 p-5">
        @if ($user->avatar)
            <img src="{{ $user->avatar }}" alt="" class="h-14 w-14 rounded-2xl object-cover">
        @else
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-600 text-xl font-bold text-white">{{ mb_substr($user->name, 0, 1) }}</span>
        @endif
        <div class="min-w-0 flex-1">
            <p class="truncate font-semibold text-ink-900 dark:text-sand-100">{{ $user->name }}</p>
            <p class="truncate text-sm text-ink-900/50 dark:text-sand-100/50" dir="ltr">{{ $user->email }}</p>
        </div>
        @unless ($user->hasVerifiedEmail())
            <a href="{{ route('verification.notice') }}" class="chip bg-gold-500/15 text-gold-700 dark:text-gold-300">تأكيد البريد</a>
        @endunless
    </div>

    {{-- Prayer settings --}}
    <h2 class="section-title mb-3">{{ __('prayer.method') }} & {{ __('prayer.location') }}</h2>
    <form method="POST" action="{{ route('account.prayer') }}" class="card mb-5 space-y-4 p-5">
        @csrf
        <div>
            <label class="label" for="prayer_method">{{ __('prayer.method') }}</label>
            <select id="prayer_method" name="prayer_method" class="field">
                @foreach ($methods as $key => $m)
                    <option value="{{ $key }}" @selected($user->prayer_method === $key)>{{ $m['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="label">مذهب حساب العصر</label>
            <div class="grid grid-cols-2 gap-2">
                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border p-3 text-sm {{ $user->asr_madhhab !== 'Hanafi' ? 'border-primary-500 bg-primary-50 text-primary-800 dark:bg-white/5 dark:text-sand-100' : 'border-sand-200 dark:border-white/10' }}">
                    <input type="radio" name="asr_madhhab" value="Standard" class="hidden" @checked($user->asr_madhhab !== 'Hanafi')> الجمهور
                </label>
                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border p-3 text-sm {{ $user->asr_madhhab === 'Hanafi' ? 'border-primary-500 bg-primary-50 text-primary-800 dark:bg-white/5 dark:text-sand-100' : 'border-sand-200 dark:border-white/10' }}">
                    <input type="radio" name="asr_madhhab" value="Hanafi" class="hidden" @checked($user->asr_madhhab === 'Hanafi')> الحنفي
                </label>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label" for="city">المدينة</label>
                <input id="city" name="city" value="{{ $user->city }}" class="field" placeholder="مثال: القاهرة">
            </div>
            <div>
                <label class="label" for="country">الدولة</label>
                <input id="country" name="country" value="{{ $user->country }}" class="field" placeholder="مثال: مصر">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label" for="latitude">خط العرض</label>
                <input id="latitude" name="latitude" value="{{ $user->latitude }}" class="field" dir="ltr" inputmode="decimal">
            </div>
            <div>
                <label class="label" for="longitude">خط الطول</label>
                <input id="longitude" name="longitude" value="{{ $user->longitude }}" class="field" dir="ltr" inputmode="decimal">
            </div>
        </div>
        <input type="hidden" name="timezone" id="tz-field" value="{{ $user->timezone }}">

        <button type="button" id="geo-btn" class="btn-ghost w-full">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-6-7-11a7 7 0 0 1 14 0c0 5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
            تحديد موقعي تلقائياً
        </button>

        <button type="submit" class="btn-primary w-full">{{ __('common.save') }}</button>
    </form>

    {{-- Language --}}
    <h2 class="section-title mb-3">اللغة</h2>
    <form method="POST" action="{{ route('locale.set') }}" class="card mb-5 flex flex-wrap gap-2 p-4">
        @csrf
        @foreach (config('locales.supported') as $code => $l)
            <button name="locale" value="{{ $code }}" class="chip {{ app()->getLocale() === $code ? 'bg-primary-600 text-white' : 'bg-sand-100 text-ink-900/70 dark:bg-white/5 dark:text-sand-100/70' }}">
                {{ $l['flag'] }} {{ $l['name'] }}
            </button>
        @endforeach
    </form>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-outline w-full text-red-600">{{ __('common.logout') }}</button>
    </form>

    <script>
        document.getElementById('geo-btn')?.addEventListener('click', function () {
            if (!navigator.geolocation) return;
            this.textContent = '…';
            navigator.geolocation.getCurrentPosition((pos) => {
                document.getElementById('latitude').value = pos.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = pos.coords.longitude.toFixed(6);
                try { document.getElementById('tz-field').value = Intl.DateTimeFormat().resolvedOptions().timeZone; } catch (e) {}
                this.textContent = '✓';
            }, () => { this.textContent = 'تعذّر تحديد الموقع'; });
        });
    </script>
</x-layouts.app>
