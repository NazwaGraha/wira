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
        Schema::table('competition_events', function (Blueprint $table) {
            if (! Schema::hasColumn('competition_events', 'is_info_menu_visible')) {
                $table->boolean('is_info_menu_visible')->default(true)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_events', function (Blueprint $table) {
            if (Schema::hasColumn('competition_events', 'is_info_menu_visible')) {
                $table->dropColumn('is_info_menu_visible');
            }
        });
    }
};
