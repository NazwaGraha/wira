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
            if (! Schema::hasColumn('competition_events', 'display_mode')) {
                $table->string('display_mode')->default('auto')->after('is_waves_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_events', function (Blueprint $table) {
            if (Schema::hasColumn('competition_events', 'display_mode')) {
                $table->dropColumn('display_mode');
            }
        });
    }
};
