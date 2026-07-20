@php($loc = config('locales.supported.'.app()->getLocale(), ['dir' => 'rtl']))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $loc['dir'] }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1">
    <meta name="theme-color" content="#0f382d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <title>{{ $title ?? __('common.app_name') }} · {{ __('common.tagline') }}</title>

    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Reem+Kufi:wght@500;600;700&family=Amiri:wght@400;700&family=Amiri+Quran&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pattern-bg min-h-dvh">
    <div class="mx-auto flex min-h-dvh max-w-md flex-col">
        {{-- Top bar --}}
        @auth
        <header class="sticky top-0 z-30 flex items-center justify-between gap-3 border-b border-black/5 bg-white/80 px-4 py-3 backdrop-blur-lg dark:border-white/5 dark:bg-ink-900/80">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-primary-600 text-gold-300 shadow-sm">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><path d="M12 2c.4 0 .75.24.9.6l1.7 4.02 4.35.36c.39.03.72.3.84.67.12.37 0 .78-.3 1.02l-3.3 2.74 1 4.25c.09.38-.06.78-.38 1-.32.23-.74.24-1.07.03L12 15.9l-3.74 2.25c-.33.2-.75.2-1.07-.03a.98.98 0 0 1-.38-1l1-4.25-3.3-2.74a.97.97 0 0 1-.3-1.02c.12-.37.45-.64.84-.67l4.35-.36 1.7-4.02c.15-.36.5-.6.9-.6z"/></svg>
                </span>
                <span class="font-display text-xl font-bold text-primary-800 dark:text-sand-100">{{ __('common.app_name') }}</span>
            </a>

            <div class="flex items-center gap-2">
                {{-- Streak --}}
                <span class="chip bg-gold-500/15 text-gold-700 dark:text-gold-300">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor"><path d="M12 2s5 4.5 5 9a5 5 0 0 1-10 0c0-1.5.6-2.8 1.2-3.8C8.9 8.9 9 10 10 10c1.1 0 1-1.6.5-3.2C10 5 12 2 12 2z"/></svg>
                    {{ $overallStreak ?? auth()->user()->overall_streak }}
                </span>
                {{-- Points --}}
                <span class="chip bg-primary-600/10 text-primary-700 dark:text-primary-300">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor"><path d="M12 2l2.4 6.9H22l-6 4.4 2.3 7-6.3-4.5L5.7 20l2.3-7-6-4.4h7.6z"/></svg>
                    {{ number_format(auth()->user()->display_points) }}
                </span>
                <button onclick="Saout.toggleTheme()" class="flex h-9 w-9 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 dark:bg-white/5 dark:text-sand-100" aria-label="theme">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M21.64 13a9 9 0 1 1-10.63-10.6 1 1 0 0 1 1.1 1.36 7 7 0 0 0 8.17 8.17 1 1 0 0 1 1.36 1.07z"/></svg>
                </button>
            </div>
        </header>
        @endauth

        {{-- Content --}}
        <main class="flex-1 px-4 pb-28 pt-4">
            {{ $slot }}
        </main>

        {{-- Bottom navigation --}}
        @auth
        @php($route = request()->route()?->getName())
        <nav class="bottom-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ str_starts_with($route ?? '', 'dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 11l9-7 9 7"/><path d="M5 10v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-9"/></svg>
                {{ __('nav.home') }}
            </a>
            <a href="{{ route('quran') }}" class="nav-item {{ str_starts_with($route ?? '', 'quran') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 6c-1.5-1-4-1.5-6-1.5S3 5 3 5v13s1-.5 3-.5 4.5.5 6 1.5c1.5-1 4-1.5 6-1.5s3 .5 3 .5V5s-1-.5-3-.5-4.5.5-6 1.5z"/><path d="M12 6v13"/></svg>
                {{ __('nav.quran') }}
            </a>
            <a href="{{ route('azkar') }}" class="nav-item {{ str_starts_with($route ?? '', 'azkar') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/></svg>
                {{ __('nav.azkar') }}
            </a>
            <a href="{{ route('progress') }}" class="nav-item {{ str_starts_with($route ?? '', 'progress') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/></svg>
                {{ __('nav.progress') }}
            </a>
            <a href="{{ route('account') }}" class="nav-item {{ str_starts_with($route ?? '', 'account') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                {{ __('nav.account') }}
            </a>
        </nav>
        @endauth
    </div>
</body>
</html>
