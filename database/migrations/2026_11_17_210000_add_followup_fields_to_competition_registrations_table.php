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
        Schema::table('competition_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('competition_registrations', 'has_pending_followup')) {
                $table->boolean('has_pending_followup')->default(false)->after('checkin_notes');
            }
            if (! Schema::hasColumn('competition_registrations', 'followup_history')) {
                $table->json('followup_history')->nullable()->after('has_pending_followup');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_registrations', function (Blueprint $table) {
            $cols = ['has_pending_followup', 'followup_history'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('competition_registrations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
