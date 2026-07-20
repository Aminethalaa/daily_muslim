# Saout · صوت

A gamified web app that helps Muslims keep up with their daily worship —
**Salat, Quran, Azkar, and Sadaka** — with reminders, streaks, badges, and a
daily recap email. Arabic-first, installable as a PWA, hosted on **saout.net**.

> *Saout* (صوت) = "voice / sound" — evoking the call to prayer: a gentle voice
> reminding you toward worship.

## Status

Planning phase. The full product & technical framework is in
**[`docs/FRAMEWORK.md`](docs/FRAMEWORK.md)** — review it before implementation begins.

## Planned stack

- **Laravel 11 + Livewire 3 + Alpine.js + Tailwind CSS** (fits shared cPanel/PHP hosting)
- **MySQL**, installable **PWA** with web push, Arabic/RTL-first with full i18n
- Prayer times computed locally (`islamic-network/prayer-times`)
- Auth: email/password + Google · Notifications: web push + email

## Feature pillars

| Pillar | What it does |
|---|---|
| 🕌 Salat | Prayer times (auto + manual), per-prayer status tracking |
| 📖 Quran | Reader + audio + reading/khatma tracker |
| 📿 Azkar | Built-in library with counters + simple checklist |
| 🤲 Sadaka | Private amounts, categories, and giving goals |
| 🏆 Gamification | XP/levels, streaks, badges, private group leaderboards |
| 🔔 Reminders | Per-prayer push, azkar/Quran nudges, daily recap & weekly emails |

See [`docs/FRAMEWORK.md`](docs/FRAMEWORK.md) for the full specification and roadmap.
