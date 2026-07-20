<?php

namespace App\Services;

use Carbon\Carbon;
use IntlDateFormatter;

/**
 * Formats a Gregorian date as an Umm al-Qura Hijri date using PHP's intl
 * extension (no external package). Falls back gracefully if intl is missing.
 */
class HijriDate
{
    public function format(Carbon $date, string $locale = 'ar', int $offsetDays = 0): string
    {
        $date = $date->copy()->addDays($offsetDays);

        if (! class_exists(IntlDateFormatter::class)) {
            return $date->format('Y-m-d');
        }

        $localeId = ($locale === 'ar' ? 'ar_SA' : $locale).'@calendar=islamic-umalqura';

        $fmt = new IntlDateFormatter(
            $localeId,
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            $date->getTimezone()->getName(),
            IntlDateFormatter::TRADITIONAL,
            'd MMMM yyyy'
        );

        return $fmt->format($date) ?: $date->format('Y-m-d');
    }
}
