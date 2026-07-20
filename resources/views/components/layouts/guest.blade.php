@php($loc = config('locales.supported.'.app()->getLocale(), ['dir' => 'rtl']))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $loc['dir'] }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f382d">
    <title>{{ $title ?? __('common.app_name') }} · {{ __('common.tagline') }}</title>

    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Reem+Kufi:wght@500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pattern-bg min-h-dvh">
    <div class="mx-auto flex min-h-dvh max-w-md flex-col justify-center px-6 py-10">
        {{-- Brand --}}
        <div class="mb-8 text-center">
            <span class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-primary-600 text-gold-300 shadow-lg shadow-primary-900/20">
                <svg viewBox="0 0 24 24" class="h-9 w-9" fill="currentColor"><path d="M12 2c.4 0 .75.24.9.6l1.7 4.02 4.35.36c.39.03.72.3.84.67.12.37 0 .78-.3 1.02l-3.3 2.74 1 4.25c.09.38-.06.78-.38 1-.32.23-.74.24-1.07.03L12 15.9l-3.74 2.25c-.33.2-.75.2-1.07-.03a.98.98 0 0 1-.38-1l1-4.25-3.3-2.74a.97.97 0 0 1-.3-1.02c.12-.37.45-.64.84-.67l4.35-.36 1.7-4.02c.15-.36.5-.6.9-.6z"/></svg>
            </span>
            <h1 class="font-display text-4xl font-bold text-primary-800 dark:text-sand-100">{{ __('common.app_name') }}</h1>
            <p class="mt-1 text-sm text-ink-900/60 dark:text-sand-100/60">{{ __('common.tagline') }}</p>
        </div>

        <div class="card p-6">
            {{ $slot }}
        </div>

        <p class="mt-6 text-center text-xs text-ink-900/40 dark:text-sand-100/40">
            {{ __('common.app_name') }} — saout.net
        </p>
    </div>
</body>
</html>
