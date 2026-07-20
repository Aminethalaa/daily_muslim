# Content Sources & Attribution

Religious content bundled with Saout, and where it comes from.

## Quran text (`resources/data/quran/`)

- **Source:** [risan/quran-json](https://github.com/risan/quran-json), which uses
  the **Tanzil** Uthmani Quran text ([tanzil.net](https://tanzil.net)).
- **Format:** split per surah (`surah_{id}.json`) plus an `index.json` of surah
  metadata (name, transliteration, Meccan/Medinan, verse count).
- **Terms:** the Tanzil Quran text is provided for free, non-commercial use with
  the condition that it is not modified. We display it verbatim. Retain this
  attribution.

## Recitation audio

- **Source:** [Islamic Network CDN](https://cdn.islamic.network) —
  per-surah audio, reciter **Mishary Rashid Alafasy** (`ar.alafasy`, 128kbps).
- Streamed at runtime (not bundled). Requires network; cached by the browser.

## Azkar (`database/seeders/AzkarSeeder.php`)

- Curated authentic adhkar (morning, evening, after-salah, sleep) with their
  references (mostly from *Hisn al-Muslim* / the Sunnah). Seeded into the
  database via `php artisan db:seed`.

## Prayer times & Hijri dates

- Computed locally (`App\Services\PrayerTimes`, standard astronomical algorithm)
  and PHP `intl` (Umm al-Qura) — no third-party content.

---

If you replace or extend any dataset, record its source and license here.
