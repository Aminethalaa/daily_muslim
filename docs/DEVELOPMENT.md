# Saout — Development & Deployment

## Local development

Requirements: **PHP ≥ 8.3**, **Composer**, **Node ≥ 20**.

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite for local dev)
touch database/database.sqlite
php artisan migrate

# 4. Build assets + run
npm run build          # or: npm run dev  (hot reload)
php artisan serve
```

Open http://127.0.0.1:8000. Register an account, or use the login/Google buttons.

The app is **Arabic-first (RTL)** by default. Language can be switched from the
Account page (Arabic / English / French scaffolded).

### Configuration you'll want

- `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` — enable "Continue with Google"
  (create OAuth credentials in Google Cloud; redirect URI
  `{APP_URL}/auth/google/callback`). Without these, the Google button shows a
  friendly "not enabled yet" message.
- `PRAYER_DEFAULT_METHOD` — default calculation method (`MWL`, `Makkah`,
  `Egypt`, `ISNA`, `Karachi`, …). Users can override per-account.
- Prayer times fall back to Makkah until a user sets their location; the app
  captures the browser timezone at sign-up and offers one-tap geolocation on the
  Account page.

## Production deployment (cPanel)

1. **PHP 8.3+** selected for the domain; enable extensions: `pdo_mysql`,
   `mbstring`, `intl`, `gd`, `curl`, `openssl`, `zip`.
2. Upload the project (or `git clone`) **outside** `public_html`, then point the
   domain's document root to the project's `public/` directory.
3. `composer install --no-dev --optimize-autoloader` and `npm ci && npm run build`
   (or upload a pre-built `public/build`).
4. `.env`: set `APP_ENV=production`, `APP_DEBUG=false`, a fresh `APP_KEY`
   (`php artisan key:generate`), MySQL credentials (`DB_CONNECTION=mysql`, …),
   and the cPanel SMTP mail settings.
5. `php artisan migrate --force`.
6. **Cron (every minute)** — powers reminders, recap emails, streak rollovers:
   ```
   * * * * * cd /home/USER/saout && php artisan schedule:run >> /dev/null 2>&1
   ```
7. **Email deliverability:** add **SPF, DKIM, DMARC** DNS records for saout.net.

## What's built so far

- Laravel 13 + Livewire 4 + Tailwind 4, **Arabic RTL** shell, dark mode.
- **Auth:** register, login, logout, password reset, email verification,
  Google OAuth (Socialite).
- **Installable PWA:** manifest, service worker (offline shell + push handler),
  app icons.
- **i18n** infrastructure (`lang/ar`, `lang/en`) — Arabic default, English ready.
- **Prayer times** engine (self-contained, all major methods + Asr madhhab),
  auto-location + manual override, Umm al-Qura Hijri dates.
- **Home dashboard:** next-prayer countdown, tap-to-mark prayers, today's
  worship checklist (Quran / Azkar / Sadaka), level & XP bar.
- **Azkar library:** authentic seeded content (morning, evening, after-salah,
  sleep), interactive **tap counters** (Livewire + Alpine) with per-item repeat
  counts, completion persistence, and gamification (XP + streak). Seed with
  `php artisan db:seed`.
- **Quran:** bundled Uthmani text (114 surahs, offline), a searchable surah
  index with continue-reading + daily-goal progress, and a **mushaf reader**
  (adjustable font size, tap-to-bookmark ayat, per-surah Alafasy audio,
  surah navigation) with a reading-session tracker feeding the daily goal,
  khatma progress, and gamification (XP + streak). See
  [`CONTENT_SOURCES.md`](CONTENT_SOURCES.md) for attribution.
- **Gamification core:** points/XP, levels, streaks (with freezes), auditable
  `xp_events` ledger, daily anti-farming caps.
- **Full data model** for all pillars + gamification + groups + notifications.

### Next phases (see FRAMEWORK.md §11)

- Sadaka amounts/goals/charts; badges; groups & leaderboards.
- Web push wiring (VAPID) + daily recap & weekly emails via the scheduler.
- Quran enhancements: per-ayah audio highlighting, page (mushaf) navigation,
  offline audio download.
