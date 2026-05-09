<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('countdown_sequences', 'loop_count')) {
            Schema::table('countdown_sequences', function (Blueprint $table) {
                $table->unsignedInteger('loop_count')->default(1)->after('paused_seconds');
            });
        }
    }

    public function down(): void
    {
        Schema::table('countdown_sequences', function (Blueprint $table) {
            if (Schema::hasColumn('countdown_sequences', 'loop_count')) {
                $table->dropColumn('loop_count');
            }
        });
    }
};
