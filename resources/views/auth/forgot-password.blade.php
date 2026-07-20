<x-layouts.guest>
    <h2 class="mb-1 text-center font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('auth.reset_title') }}</h2>

    @if (session('status'))
        <div class="my-4 rounded-2xl bg-primary-50 px-4 py-3 text-sm text-primary-800">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="label" for="email">{{ __('auth.email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="field" dir="ltr" placeholder="you@example.com">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-primary w-full">{{ __('auth.reset_send') }}</button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:underline">{{ __('auth.login') }}</a>
    </p>
</x-layouts.guest>
