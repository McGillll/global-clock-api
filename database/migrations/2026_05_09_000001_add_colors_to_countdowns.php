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
        Schema::table('countdowns', function (Blueprint $table) {
            $table->string('background_color')->default('#0f172a')->after('is_active');
            $table->string('font_color')->default('#f1f5f9')->after('background_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countdowns', function (Blueprint $table) {
            $table->dropColumn('background_color');
            $table->dropColumn('font_color');
        });
    }
};
