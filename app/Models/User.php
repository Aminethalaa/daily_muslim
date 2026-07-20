<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'google_id', 'avatar',
        'locale', 'timezone', 'country', 'city', 'latitude', 'longitude',
        'prayer_method', 'asr_madhhab', 'hijri_offset', 'onboarded_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarded_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'float',
            'longitude' => 'float',
            'hijri_offset' => 'integer',
        ];
    }

    // ---- Relationships ------------------------------------------------------

    public function stats(): HasOne
    {
        return $this->hasOne(UserStat::class);
    }

    public function notificationSetting(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function quranProgress(): HasOne
    {
        return $this->hasOne(QuranProgress::class);
    }

    public function prayerLogs(): HasMany
    {
        return $this->hasMany(PrayerLog::class);
    }

    public function quranSessions(): HasMany
    {
        return $this->hasMany(QuranSession::class);
    }

    public function quranBookmarks(): HasMany
    {
        return $this->hasMany(QuranBookmark::class);
    }

    public function azkarLogs(): HasMany
    {
        return $this->hasMany(AzkarLog::class);
    }

    public function sadakaLogs(): HasMany
    {
        return $this->hasMany(SadakaLog::class);
    }

    public function streaks(): HasMany
    {
        return $this->hasMany(Streak::class);
    }

    public function xpEvents(): HasMany
    {
        return $this->hasMany(XpEvent::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    // ---- Convenience accessors (safe defaults) ------------------------------

    public function getDisplayPointsAttribute(): int
    {
        return (int) ($this->stats?->points ?? 0);
    }

    public function getLevelNumberAttribute(): int
    {
        return (int) ($this->stats?->level ?? 1);
    }

    public function getOverallStreakAttribute(): int
    {
        return (int) ($this->streaks->firstWhere('type', 'overall')?->current ?? 0);
    }

    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function isRtl(): bool
    {
        return (config('locales.supported.'.$this->locale.'.dir') ?? 'rtl') === 'rtl';
    }
}
