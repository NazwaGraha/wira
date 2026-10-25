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
        if (Schema::hasTable('competition_participant_teams') && ! Schema::hasColumn('competition_participant_teams', 'members_file')) {
            Schema::table('competition_participant_teams', function (Blueprint $table) {
                $table->string('members_file')->nullable()->after('members_list');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('competition_participant_teams') && Schema::hasColumn('competition_participant_teams', 'members_file')) {
            Schema::table('competition_participant_teams', function (Blueprint $table) {
                $table->dropColumn('members_file');
            });
        }
    }
};
