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
        Schema::table('competition_categories', function (Blueprint $table) {
            $table->unsignedInteger('max_quota')->default(50)->nullable()->after('max_team_members');
            $table->unsignedInteger('max_teams_per_school_pa')->default(2)->after('max_quota');
            $table->unsignedInteger('max_teams_per_school_pi')->default(2)->after('max_teams_per_school_pa');
            $table->unsignedInteger('max_teams_per_school_mix')->default(2)->after('max_teams_per_school_pi');
            $table->boolean('is_quota_enforced')->default(true)->after('max_teams_per_school_mix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_categories', function (Blueprint $table) {
            $table->dropColumn([
                'max_quota',
                'max_teams_per_school_pa',
                'max_teams_per_school_pi',
                'max_teams_per_school_mix',
                'is_quota_enforced',
            ]);
        });
    }
};
