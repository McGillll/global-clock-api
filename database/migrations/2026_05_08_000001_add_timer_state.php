<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add timer state fields to countdown_sequences
        if (!Schema::hasColumn('countdown_sequences', 'status')) {
            Schema::table('countdown_sequences', function (Blueprint $table) {
                $table->enum('status', ['pending', 'running', 'paused', 'finished'])->default('pending')->after('paused_at');
            });
        }

        if (!Schema::hasColumn('countdown_sequences', 'current_item_position')) {
            Schema::table('countdown_sequences', function (Blueprint $table) {
                $table->integer('current_item_position')->default(0)->after('status');
            });
        }

        if (!Schema::hasColumn('countdown_sequences', 'paused_seconds')) {
            Schema::table('countdown_sequences', function (Blueprint $table) {
                $table->integer('paused_seconds')->default(0)->after('current_item_position');
            });
        }
    }

    public function down(): void
    {
        Schema::table('countdown_sequences', function (Blueprint $table) {
            if (Schema::hasColumn('countdown_sequences', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('countdown_sequences', 'current_item_position')) {
                $table->dropColumn('current_item_position');
            }
            if (Schema::hasColumn('countdown_sequences', 'paused_seconds')) {
                $table->dropColumn('paused_seconds');
            }
        });
    }
};
