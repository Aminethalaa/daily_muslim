# Saout (صوت) — Framework & Technical Specification

> A gamified companion that helps Muslims keep up with their daily worship —
> Salat, Quran, Azkar, and Sadaka — with reminders, streaks, and a daily recap email.
> Arabic-first, installable as a PWA, hosted on **saout.net**.

**Status:** Draft v1 (framework for review) · **Last updated:** 2026-07-20

---

## 1. Vision & Product Principles

**Vision.** Make consistent daily worship easier and more joyful by combining
gentle reminders, honest self-tracking, and light, respectful gamification —
without turning ibadah into a game for its own sake.

**Product principles**

1. **Ibadah first, gamification second.** Points and streaks are *encouragement*
   (تشجيع), never the goal. Language and design stay humble and free of riyā'
   (showing off). Public competition is always opt-in.
2. **Arabic-first, RTL-native.** Arabic is the default and the design is built
   for RTL from the ground up — not translated later.
3. **Private by default.** Spiritual and financial data (especially sadaka
   amounts) are personal. Nothing is shared unless the user explicitly opts in.
4. **Works on any phone.** Installable PWA, fast on low-end devices and slow
   networks, usable offline for daily tracking.
5. **Gentle, not guilt-driven.** Missed days are met with encouragement and
   easy recovery (streak freezes, qada logging), never shame.

**Non-goals (for now)**

- Not a fatwa/Q&A or scholarly reference app.
- Not a social network; social features are limited to small trusted groups.
- No native iOS/Android app in the first phases (kept open for later).

---

## 2. Target Users

- **The consistent striver** — prays regularly, wants to track and improve
  (streaks, khatma goals).
- **The rebuilder** — getting back into a routine, needs gentle reminders and
  small wins.
- **The family/friends circle** — a few people who want to encourage each other
  privately (a family group, a study halaqa).

Primary audience is **global/mixed**; the app auto-detects location and asks the
user to confirm their prayer calculation method on first use.

---

## 3. Feature Set

### 3.1 Salat (الصلاة) — daily prayer tracking

- Five daily prayers computed from the user's location and chosen method.
- Each prayer can be marked with a **status**: prayed on time, prayed in
  congregation (جماعة), prayed late, made up (قضاء), or not yet.
- Optional tracking of **Sunnah/Nafl** (rawatib), **Witr**, **Tahajjud**,
  **Duha**, and **Jumu'ah**.
- Prayer times screen with the next prayer countdown and the day's timetable.
- Qibla direction (compass) — nice-to-have in a later phase.

### 3.2 Quran (القرآن) — read, listen, track

- **Reader:** Uthmani mushaf text, page/surah/juz navigation, adjustable font
  size, night mode, and a proper Quran typeface.
- **Audio:** streamed recitation per ayah/surah from a public CDN, with a choice
  of a few reciters; continue-where-you-left-off.
- **Tracker:** log reading/listening by surah + ayah range or by pages/juz; set
  a **daily goal** (e.g. 4 pages) and a **khatma goal** (finish in N days).
- **Bookmarks & last-read** position; khatma counter and progress ring.

### 3.3 Azkar (الأذكار) — remembrance

- Built-in **library** (from an open Hisnul Muslim dataset): morning, evening,
  after-salah, sleep, waking, and general azkar — with Arabic text,
  transliteration, translation, repeat counts, and references.
- **Tap counters** that track completion of each category.
- **Simple check-off** mode for users who read from a physical book
  ("Morning azkar done" / "Evening azkar done").

### 3.4 Sadaka (الصدقة) — charity tracking

- Daily check-off plus **optional amount + category** (mosque, poor, family,
  water/well, general, other) and a private note.
- **Goals:** monthly/weekly giving targets with progress and simple charts.
- **Strictly private:** amounts are never shown to other users or on
  leaderboards. Leaderboards, if used, count *acts of giving*, not sums.

### 3.5 Custom habits (later)

- User-defined daily habits (e.g. "istighfar 100×", "read a hadith") that
  plug into the same tracking + gamification engine.

### 3.6 Gamification

- **XP & levels** — completing acts grants XP; users climb named levels.
- **Streaks** — per-habit daily streaks with **streak freezes** (grace days)
  to avoid harsh breaks.
- **Badges/achievements** — milestones (first week, 40-day streak, first khatma,
  Ramadan completion, etc.).
- **Groups & leaderboards** — small private groups (family/friends) with an
  opt-in, privacy-aware leaderboard based on consistency, not amounts.

See [§7](#7-gamification-design) for the full economy and anti-gaming rules.

### 3.7 Notifications

- **Per-prayer push** reminders at each prayer time (tap to mark as prayed).
- **Azkar/Quran nudges** — morning/evening azkar, daily Quran goal.
- **Daily recap email** — evening summary of today's acts, streaks, XP, and
  tomorrow's goals.
- **Weekly progress summary** — stats, best streaks, and encouragement.

See [§8](#8-notifications-system) for scheduling and delivery design.

---

## 4. Technical Architecture

### 4.1 Stack

| Layer | Choice | Notes |
|---|---|---|
| Framework | **Laravel 11** | Fits cPanel/PHP; batteries-included auth, queues, scheduler, mail. |
| Interactivity | **Livewire 3 + Alpine.js** | Rich, app-like UI without an SPA build pipeline. |
| Styling | **Tailwind CSS** | With RTL logical utilities; small CSS build. |
| Database | **MySQL 8** (or MariaDB 10.6+) | Standard on cPanel. |
| PWA | Service worker + Web App Manifest | Installable, offline shell, web push. |
| Auth | Laravel Breeze/Fortify + **Laravel Socialite** (Google) | Email/password + Google OAuth. |
| Prayer times | `islamic-network/prayer-times` (PHP) | Local computation, no external API. |
| Hijri dates | `islamic-network/hijri-date` (PHP) | Hijri calendar display. |
| Web push | `minishlink/web-push` (VAPID) | Free; no third-party push cost. |
| Charts | Lightweight JS (e.g. Chart.js via CDN or bundled) | For stats/goals. |

> **Why Livewire over an SPA:** shared cPanel hosting is happiest with
> server-rendered PHP. Livewire gives an interactive feel while keeping the
> deployment a single Laravel app with a minimal front-end build.

### 4.2 Shared-hosting realities (cPanel) & how we handle them

Shared hosting has no long-running processes, so the design leans on **cron**:

- **Scheduler.** A single cPanel cron entry runs every minute:
  `php /path/artisan schedule:run`. All periodic work (sending due reminders,
  recap emails, streak rollovers, leaderboard refresh) is registered in
  Laravel's scheduler.
- **Queues.** Use the **database** queue driver. Jobs (push/email sends) are
  processed by a scheduled `queue:work --stop-when-empty` invoked from the
  scheduler, so no persistent worker is required.
- **Web push.** VAPID keys generated once; pushes sent from queued jobs via
  `minishlink/web-push`. No paid service.
- **Email.** cPanel SMTP (per your choice). **Deliverability note:** to keep
  recap emails out of spam we must set **SPF, DKIM, and DMARC** DNS records for
  saout.net and respect the host's hourly send limits. Sends are throttled and
  batched from the queue. (A transactional provider remains an easy swap later —
  mail is config-driven.)
- **Document root.** cPanel must point the domain at Laravel's `public/`
  directory (or use a safe `public_html` shim), and PHP must be **8.2+**.

> **Hosting requirements to confirm** (see [§13](#13-open-questions--requirements)):
> PHP ≥ 8.2, Composer available (or build/upload vendor), cron access, ability
> to set the web root to `public/`, and MySQL access.

### 4.3 Internationalization (i18n) & RTL

- All UI strings live in translation files (`lang/ar/*`, `lang/ar.json`).
  Arabic ships first; the structure supports adding any language with no code
  changes.
- Locale + `dir` are set per-user; layout uses **RTL by default** with Tailwind
  logical utilities (`ps-*`, `pe-*`, `ms-*`, `me-*`, `text-start/end`).
- **Fonts:** a clean Arabic UI face (e.g. IBM Plex Sans Arabic / Cairo) for the
  interface and a dedicated **Mushaf typeface** (e.g. an Uthmanic/Amiri font)
  for Quran text. Fonts are self-hosted.
- Numbers, dates (Gregorian + **Hijri**), and prayer times are localized and
  timezone-aware per user.

### 4.4 Content data sources (with attribution)

- **Quran text:** an open Uthmani dataset (e.g. Tanzil / Quran.com QUL),
  bundled locally, used per each source's terms with attribution.
- **Recitation audio:** streamed from a public recitation CDN (per-ayah/surface
  audio), with reciter attribution.
- **Azkar:** an open-source Hisnul Muslim JSON dataset, seeded into the DB with
  attribution.

> Exact datasets are pinned during implementation and their licenses recorded
> in `docs/CONTENT_SOURCES.md`.

### 4.5 PWA & offline

- **Installable** via manifest (name, icons, theme color, RTL-aware).
- **Offline:** app shell + today's tracking cached; logging works offline and
  syncs when back online. Quran text can be cached for offline reading; audio is
  online-only (optional per-surah download later).
- Web push handled by the service worker (works even when the tab is closed on
  supported browsers).

---

## 5. Data Model (high level)

Core entities (MySQL tables). Field lists are indicative, not final.

**Users & settings**
- `users` — name, email, password, `google_id`, `locale` (default `ar`),
  `timezone`, `country`, `city`, `lat`, `lng`, `prayer_method`, `asr_madhhab`,
  `hijri_offset`, avatar, flags.
- `notification_settings` — per-user toggles/times for each channel & type.
- `push_subscriptions` — endpoint, keys, device label.

**Worship logs (one row per act/day)**
- `prayer_logs` — `user_id`, `date`, `prayer` (fajr…isha + sunnah types),
  `status` (on_time/jamaah/late/qada/missed), `logged_at`.
- `quran_sessions` — `user_id`, `date`, `mode` (read/listen), from/to
  (surah+ayah), `pages`, `juz`, `duration`, `reciter`.
- `quran_progress` — last-read position, khatma count, current khatma goal.
- `quran_bookmarks` — surah/ayah + note.
- `azkar_logs` — `user_id`, `date`, `category` (morning/evening/…),
  `completed_count`, `total`, `completed_at`.
- `sadaka_logs` — `user_id`, `date`, `amount?`, `currency`, `category`,
  `note?`, always private.
- `sadaka_goals` — period, target amount, progress.
- `habit_logs` (later) — custom habits.

**Content (seeded, read-mostly)**
- `azkar_categories`, `azkar_items` — text, transliteration, translation,
  `repeat_count`, `reference`.
- (Quran text/audio referenced from bundled assets/dataset, not a heavy table.)

**Gamification**
- `user_stats` — total XP, level, computed rollups.
- `xp_events` — ledger: `user_id`, `source`, `points`, `ref`, `created_at`
  (auditable, powers anti-gaming).
- `streaks` — `user_id`, `type`, `current`, `longest`, `last_date`,
  `freezes_available`.
- `badges` (catalog) + `user_badges` (earned, with timestamp).
- `levels` (catalog) — thresholds + names.

**Social**
- `groups` — name, owner, privacy, invite code.
- `group_members` — role, joined_at.
- Leaderboards are computed (cached snapshot) from consistency metrics.

**System**
- `jobs`, `failed_jobs` (DB queue), `notifications_log`, standard Laravel tables.

---

## 6. Screens & Navigation (information architecture)

Bottom nav (RTL): **الرئيسية (Home) · القرآن (Quran) · الأذكار (Azkar) · التقدّم (Progress) · حسابي (Account)**

- **Home / Today** — next prayer countdown, today's checklist across all four
  pillars, current streaks, XP toward next level, quick "mark done" actions.
- **Prayer times** — full timetable, method/location settings, per-prayer status.
- **Quran** — reader (text + audio), tracker, goals, bookmarks, khatma progress.
- **Azkar** — categories, counters, check-off, favorites.
- **Sadaka** — quick log, amounts/categories, goals & charts.
- **Progress** — streaks, badges, level, history calendar/heatmap, weekly stats.
- **Groups** — create/join a private group, opt-in leaderboard.
- **Account/Settings** — profile, language, prayer method, notification prefs,
  privacy, install prompt, donate.
- **Onboarding** — language confirm → location/method confirm → pick goals →
  enable notifications → install PWA prompt.

---

## 7. Gamification Design

**XP sources (indicative values — tunable):**

| Act | XP |
|---|---|
| Fard prayer on time | 10 (+5 in jamaah, +3 Fajr/Isha bonus) |
| Sunnah/Witr/Tahajjud | 3–5 each |
| Daily Quran goal met | 15 |
| Morning / Evening azkar | 8 each |
| Sadaka logged | 8 |
| Full day (all core pillars) | +20 bonus |

**Levels.** A gentle curve (e.g. level *n* needs `~50·n·(n+1)` XP) with Arabic
names conveying growth in worship (not status/wealth).

**Streaks.**
- Independent streaks per habit (salat, Quran, azkar, sadaka) + an overall
  "consistency" streak.
- **Streak freezes:** users earn a freeze for strong weeks; a missed day
  consumes a freeze instead of resetting to zero. Encouraging, not punishing.

**Badges.** First day, 7-day, 40-day, 100-day streaks; first khatma; a full
Ramadan; congregation milestones; azkar consistency; etc.

**Anti-gaming (integrity).**
- Prayers/acts can only be logged for **today or a short back-window** (e.g.
  same-day and yesterday), never for the future.
- **Daily XP caps** per category prevent farming.
- All XP is written to an auditable `xp_events` ledger so streaks/levels can be
  recomputed and abuse detected.
- Leaderboards rank **consistency %**, never sadaka amounts, and stay within
  small opt-in groups by default.

**Tone.** Copy avoids implying reward *from Allah* for using the app; it frames
points/streaks as a personal encouragement tool only.

---

## 8. Notifications System

**Channels:** Web Push (VAPID) + Email (cPanel SMTP).

**Scheduling model.** Every user has a timezone; the Laravel scheduler runs each
minute and dispatches due notifications:

- **Per-prayer push:** recomputed daily per user from their method/location;
  a job fires at each prayer time (with optional pre-prayer lead time).
- **Azkar/Quran nudges:** user-configurable morning/evening times.
- **Daily recap email:** sent in the user's evening (after Isha window),
  summarizing completed acts, streaks, XP gained, and tomorrow's goals.
- **Weekly summary:** once a week (user-chosen day), with trends and best streaks.

**Delivery hygiene.**
- Respect per-user quiet hours and channel toggles.
- Batch + throttle email to stay under shared-host limits; retries via the DB
  queue with backoff; log every send.
- Every email has an unsubscribe/preferences link; push respects browser
  permission state and prunes dead subscriptions.

---

## 9. Security & Privacy

- Standard Laravel hardening: hashed passwords, CSRF, signed URLs, rate limiting,
  email verification, secure session cookies, validation everywhere.
- **Sensitive data:** sadaka amounts and all worship logs are private to the
  user; never exposed via groups, leaderboards, or the API.
- Group leaderboards share only consistency metrics the user has opted into.
- Minimal PII; clear data export & account deletion.
- Secrets (Google OAuth, VAPID, SMTP) in `.env`, never committed.
- Content datasets used within their licenses, with attribution recorded.

---

## 10. Branding — "Saout" (صوت)

**Concept.** *Saout* = "voice / sound" — evoking the call to prayer (الأذان):
a gentle voice reminding you toward worship.

- **Palette (proposed):** deep emerald/teal green (calm, spiritual) + warm gold
  accent + soft neutrals; full **dark mode**.
- **Type:** modern Arabic UI face for the interface; a classic Mushaf face for
  Quran text.
- **Feel:** calm, spacious, respectful; subtle motion; celebratory but humble
  micro-moments for milestones.
- Logo/mark, app icons, and PWA splash to be designed in Phase 1.

---

## 11. Phased Roadmap

**Phase 0 — Foundations**
- Laravel + Livewire + Tailwind project, RTL/Arabic shell, i18n scaffolding.
- Auth (email/password + Google), user model & settings, PWA manifest + service
  worker, base layout & navigation, DB migrations for core tables.
- cPanel deployment guide + cron/scheduler wiring.

**Phase 1 — Salat MVP**
- Prayer time computation (auto + manual), prayer times screen, salat tracking
  with statuses, Home/Today dashboard.
- Core gamification: XP, levels, salat streaks, first badges.
- Per-prayer push + **daily recap email**.

**Phase 2 — Quran & Azkar**
- Quran reader (text) + audio + tracker + goals/khatma + bookmarks.
- Azkar library + counters + check-off.
- More badges; weekly summary email; azkar/Quran reminders.

**Phase 3 — Sadaka & Social**
- Sadaka logging (amounts/categories/goals + charts).
- Groups + opt-in leaderboards; progress heatmap & richer stats.
- Offline improvements.

**Phase 4 — Polish & Growth**
- Qibla compass, custom habits, donation button, additional languages,
  onboarding polish; evaluate a native wrapper.

---

## 12. Definition of Done (per feature)

Arabic + RTL correct · works offline where applicable · covered by tests
(unit + key flows) · notifications/scheduler verified · privacy respected ·
accessible (contrast, tap targets) · documented.

---

## 13. Open Questions & Requirements

To confirm before/around Phase 0:

1. **Hosting specifics:** PHP version (need **≥ 8.2**), Composer/SSH access,
   cron availability, ability to set web root to `public/`, MySQL version.
2. **Domain email DNS:** can we add **SPF/DKIM/DMARC** for saout.net? (Critical
   for recap-email deliverability on cPanel SMTP.)
3. **Google OAuth:** who provides the Google Cloud project / client credentials?
4. **Reciter(s) & Quran dataset:** any preferred reciter or mushaf edition?
5. **Sadaka currency:** single default currency or multi-currency per user?
6. **Group size/limits:** max members per group; any global leaderboard, or
   groups only?
7. **Branding assets:** existing logo/colors, or design fresh in Phase 1?

---

## 14. Next Steps

1. You review this framework and adjust anything (features, scope, tone, brand).
2. I confirm the hosting requirements checklist ([§13](#13-open-questions--requirements)).
3. On approval, I begin **Phase 0** scaffolding on branch
   `claude/islamic-web-app-gamification-4nhbzn`.

*بإذن الله — may this be a means of benefit and consistency in worship.*
