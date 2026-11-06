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
            $table->boolean('is_countdown_mascot_visible')->default(true)->after('is_btn_handbook_visible');
            $table->string('countdown_mascot_left')->nullable()->default('images/mascot/countdown_mascot_left.png')->after('is_countdown_mascot_visible');
            $table->string('countdown_mascot_right')->nullable()->default('images/mascot/countdown_mascot_right.png')->after('countdown_mascot_left');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_events', function (Blueprint $table) {
            $table->dropColumn([
                'is_countdown_mascot_visible',
                'countdown_mascot_left',
                'countdown_mascot_right',
            ]);
        });
    }
};
