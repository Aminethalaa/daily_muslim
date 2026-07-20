<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'push_enabled' => 'bool', 'prayer_reminders' => 'bool',
        'azkar_reminders' => 'bool', 'quran_reminder' => 'bool',
        'email_recap' => 'bool', 'email_weekly' => 'bool',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
