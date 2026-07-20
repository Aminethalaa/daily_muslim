<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('push_enabled')->default(true);
            $table->boolean('prayer_reminders')->default(true);
            $table->boolean('azkar_reminders')->default(true);
            $table->boolean('quran_reminder')->default(true);
            $table->boolean('email_recap')->default(true);
            $table->boolean('email_weekly')->default(true);
            $table->unsignedTinyInteger('recap_hour')->default(21);      // local hour for daily recap
            $table->unsignedTinyInteger('weekly_day')->default(5);       // 0=Sun … 5=Fri
            $table->unsignedTinyInteger('prayer_lead_minutes')->default(0);
            $table->time('quiet_from')->nullable();
            $table->time('quiet_to')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('endpoint');
            $table->string('public_key');
            $table->string('auth_token');
            $table->string('content_encoding')->nullable();
            $table->string('device_label')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('notification_settings');
    }
};
