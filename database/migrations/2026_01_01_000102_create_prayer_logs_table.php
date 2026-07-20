<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('prayer', 20);   // fajr|dhuhr|asr|maghrib|isha|witr|tahajjud|duha|jumua
            $table->string('status', 16)->default('on_time'); // on_time|jamaah|late|qada
            $table->timestamp('logged_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date', 'prayer']);
            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_logs');
    }
};
