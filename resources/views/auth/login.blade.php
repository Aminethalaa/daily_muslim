<x-layouts.guest>
    <h2 class="mb-1 text-center font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('auth.login') }}</h2>
    <p class="mb-6 text-center text-sm text-ink-900/60 dark:text-sand-100/60">{{ __('auth.login_subtitle') }}</p>

    @if (session('status'))
        <div class="mb-4 rounded-2xl bg-primary-50 px-4 py-3 text-sm text-primary-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label" for="email">{{ __('auth.email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="field" dir="ltr" placeholder="you@example.com">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="password">{{ __('auth.password_label') }}</label>
            <input id="password" name="password" type="password" required class="field" dir="ltr">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-ink-900/70 dark:text-sand-100/70">
                <input type="checkbox" name="remember" class="rounded border-sand-300 text-primary-600 focus:ring-primary-500">
                {{ __('auth.remember') }}
            </label>
            <a href="{{ route('password.request') }}" class="font-medium text-primary-600 hover:underline">{{ __('auth.forgot') }}</a>
        </div>

        <button type="submit" class="btn-primary w-full">{{ __('auth.login') }}</button>
    </form>

    <div class="my-5 flex items-center gap-3 text-xs text-ink-900/40 dark:text-sand-100/40">
        <span class="h-px flex-1 bg-sand-200 dark:bg-white/10"></span>{{ __('auth.or') }}<span class="h-px flex-1 bg-sand-200 dark:bg-white/10"></span>
    </div>

    <a href="{{ route('google.redirect') }}" class="btn-outline w-full">
        <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="#4285F4" d="M22.5 12.2c0-.7-.1-1.4-.2-2H12v3.8h5.9a5 5 0 0 1-2.2 3.3v2.7h3.6c2.1-2 3.2-4.8 3.2-7.8z"/><path fill="#34A853" d="M12 23c2.9 0 5.4-1 7.2-2.6l-3.6-2.7c-1 .7-2.3 1-3.6 1-2.8 0-5.1-1.9-6-4.4H2.3v2.8A11 11 0 0 0 12 23z"/><path fill="#FBBC05" d="M6 14.3a6.6 6.6 0 0 1 0-4.2V7.3H2.3a11 11 0 0 0 0 9.8L6 14.3z"/><path fill="#EA4335" d="M12 5.4c1.6 0 3 .5 4.1 1.6l3.1-3.1A11 11 0 0 0 2.3 7.3L6 10.1c.9-2.6 3.2-4.7 6-4.7z"/></svg>
        {{ __('auth.continue_google') }}
    </a>

    <p class="mt-6 text-center text-sm text-ink-900/70 dark:text-sand-100/70">
        {{ __('auth.no_account') }}
        <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:underline">{{ __('auth.register') }}</a>
    </p>
</x-layouts.guest>
