<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azkar_categories', function (Blueprint $table) {
            $table->id();
            $table->string('key', 40)->unique();  // morning|evening|after_salah|sleep|wake|general
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('azkar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('azkar_categories')->cascadeOnDelete();
            $table->text('text_ar');
            $table->text('transliteration')->nullable();
            $table->text('translation_ar')->nullable();
            $table->text('translation_en')->nullable();
            $table->unsignedSmallInteger('repeat_count')->default(1);
            $table->string('reference')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('azkar_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('azkar_categories')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('completed_count')->default(0);
            $table->unsignedSmallInteger('total')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'category_id', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azkar_logs');
        Schema::dropIfExists('azkar_items');
        Schema::dropIfExists('azkar_categories');
    }
};
