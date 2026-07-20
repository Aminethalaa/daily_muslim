<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quran_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('mode', 10)->default('read'); // read|listen
            $table->unsignedTinyInteger('from_surah')->nullable();
            $table->unsignedSmallInteger('from_ayah')->nullable();
            $table->unsignedTinyInteger('to_surah')->nullable();
            $table->unsignedSmallInteger('to_ayah')->nullable();
            $table->decimal('pages', 6, 2)->default(0);
            $table->unsignedTinyInteger('juz')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('reciter')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });

        Schema::create('quran_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('last_surah')->default(1);
            $table->unsignedSmallInteger('last_ayah')->default(1);
            $table->unsignedSmallInteger('last_page')->default(1);
            $table->unsignedInteger('khatma_count')->default(0);
            $table->unsignedSmallInteger('daily_goal_pages')->default(4);
            $table->unsignedSmallInteger('khatma_goal_days')->nullable();
            $table->date('khatma_started_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('quran_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('surah');
            $table->unsignedSmallInteger('ayah');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quran_bookmarks');
        Schema::dropIfExists('quran_progress');
        Schema::dropIfExists('quran_sessions');
    }
};
