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
            if (! Schema::hasColumn('competition_events', 'is_btn_register_visible')) {
                $table->boolean('is_btn_register_visible')->default(true)->after('is_registration_open');
            }
            if (! Schema::hasColumn('competition_events', 'is_btn_status_visible')) {
                $table->boolean('is_btn_status_visible')->default(true)->after('is_btn_register_visible');
            }
            if (! Schema::hasColumn('competition_events', 'is_btn_scoreboard_visible')) {
                $table->boolean('is_btn_scoreboard_visible')->default(true)->after('is_btn_status_visible');
            }
            if (! Schema::hasColumn('competition_events', 'is_btn_handbook_visible')) {
                $table->boolean('is_btn_handbook_visible')->default(true)->after('is_btn_scoreboard_visible');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_events', function (Blueprint $table) {
            $columns = [
                'is_btn_register_visible',
                'is_btn_status_visible',
                'is_btn_scoreboard_visible',
                'is_btn_handbook_visible',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('competition_events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
