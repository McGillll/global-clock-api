<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('duration_seconds');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('countdown_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_running')->default(false);
            $table->unsignedInteger('current_item_index')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamps();
        });

        Schema::create('countdown_sequence_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('countdown_sequence_id')->constrained('countdown_sequences')->cascadeOnDelete();
            $table->foreignId('countdown_id')->constrained('countdowns')->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->unique(['countdown_sequence_id', 'position']);
        });

        Schema::create('countdown_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('countdown_sequence_id')->constrained('countdown_sequences')->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->timestamps();

            $table->unique('countdown_sequence_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countdown_shares');
        Schema::dropIfExists('countdown_sequence_items');
        Schema::dropIfExists('countdown_sequences');
        Schema::dropIfExists('countdowns');
    }
};
