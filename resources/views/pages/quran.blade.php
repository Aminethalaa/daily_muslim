<x-layouts.app :title="__('nav.quran')">
    <h1 class="mb-4 font-display text-2xl font-bold text-primary-800 dark:text-sand-100">{{ __('nav.quran') }}</h1>

    {{-- Khatma progress --}}
    <div class="card mb-5 p-5 text-center">
        <p class="font-quran text-2xl leading-loose text-primary-800 dark:text-sand-100">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
        <div class="mt-4 grid grid-cols-3 gap-3 text-center">
            <div>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-300">{{ $progress?->khatma_count ?? 0 }}</p>
                <p class="text-xs text-ink-900/50 dark:text-sand-100/50">ختمات</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-300">{{ $progress?->last_page ?? 1 }}</p>
                <p class="text-xs text-ink-900/50 dark:text-sand-100/50">آخر صفحة</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-300">{{ $progress?->daily_goal_pages ?? 4 }}</p>
                <p class="text-xs text-ink-900/50 dark:text-sand-100/50">هدف يومي</p>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-dashed border-primary-300 bg-primary-50/50 p-6 text-center dark:border-white/10 dark:bg-white/5">
        <span class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-600/10 text-primary-600 dark:text-primary-300">
            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 6c-1.5-1-4-1.5-6-1.5S3 5 3 5v13s1-.5 3-.5 4.5.5 6 1.5c1.5-1 4-1.5 6-1.5s3 .5 3 .5V5s-1-.5-3-.5-4.5.5-6 1.5z"/><path d="M12 6v13"/></svg>
        </span>
        <p class="font-semibold text-ink-900 dark:text-sand-100">المصحف والاستماع قريباً</p>
        <p class="mt-1 text-sm text-ink-900/60 dark:text-sand-100/60">قراءة المصحف كاملاً، الاستماع للتلاوة، تتبّع الورد والختمة — في المرحلة القادمة بإذن الله.</p>
    </div>
</x-layouts.app>
