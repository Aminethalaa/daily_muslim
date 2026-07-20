<x-layouts.guest>
    <h2 class="mb-6 text-center font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('auth.reset_title') }}</h2>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="label" for="email">{{ __('auth.email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required class="field" dir="ltr">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label" for="password">{{ __('auth.password_label') }}</label>
            <input id="password" name="password" type="password" required class="field" dir="ltr">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label" for="password_confirmation">{{ __('auth.password_confirm') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="field" dir="ltr">
        </div>
        <button type="submit" class="btn-primary w-full">{{ __('auth.reset_title') }}</button>
    </form>
</x-layouts.guest>
