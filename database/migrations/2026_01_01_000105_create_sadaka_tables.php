<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sadaka_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount', 12, 2)->nullable(); // optional & private
            $table->string('currency', 3)->nullable();
            $table->string('category', 32)->default('general'); // mosque|poor|family|water|general|other
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });

        Schema::create('sadaka_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('period', 10)->default('monthly'); // weekly|monthly
            $table->decimal('target_amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sadaka_goals');
        Schema::dropIfExists('sadaka_logs');
    }
};
