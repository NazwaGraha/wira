<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('competition_categories')
            ->where(function ($query) {
                $query->where('name', 'like', '%Olimpiade%')
                    ->orWhere('name', 'like', '%Olympiade%');
            })
            ->update([
                'max_teams_per_school_mix' => 3,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('competition_categories')
            ->where(function ($query) {
                $query->where('name', 'like', '%Olimpiade%')
                    ->orWhere('name', 'like', '%Olympiade%');
            })
            ->update([
                'max_teams_per_school_mix' => 2,
            ]);
    }
};
