<x-layouts.guest>
    <h2 class="mb-2 text-center font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('auth.verify_title') }}</h2>
    <p class="mb-6 text-center text-sm text-ink-900/60 dark:text-sand-100/60">{{ __('auth.verify_body') }}</p>

    @if (session('status'))
        <div class="mb-4 rounded-2xl bg-primary-50 px-4 py-3 text-center text-sm text-primary-800">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-primary w-full">{{ __('auth.verify_resend') }}</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn-ghost w-full">{{ __('common.logout') }}</button>
    </form>
</x-layouts.guest>
