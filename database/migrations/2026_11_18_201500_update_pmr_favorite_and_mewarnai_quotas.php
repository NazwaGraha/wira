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
        // 1. PMR Favorite: Kuota 1 regu per sekolah, Kategori Umum
        DB::table('competition_categories')
            ->where(function ($query) {
                $query->where('name', 'like', '%favorite%')
                    ->orWhere('name', 'like', '%favorit%')
                    ->orWhere('code', 'like', 'FAV-%');
            })
            ->update([
                'gender_category' => 'Umum',
                'max_teams_per_school_mix' => 1,
                'max_teams_per_school_pa' => 1,
                'max_teams_per_school_pi' => 1,
            ]);

        // 2. Lomba Mewarnai (Mula): Kuota maksimal 2 regu per sekolah, Kategori Umum
        DB::table('competition_categories')
            ->where(function ($query) {
                $query->where('name', 'like', '%mewarnai%')
                    ->orWhere('code', 'like', 'MEWARNAI-%');
            })
            ->update([
                'gender_category' => 'Umum',
                'max_teams_per_school_mix' => 2,
                'max_teams_per_school_pa' => 2,
                'max_teams_per_school_pi' => 2,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('competition_categories')
            ->where(function ($query) {
                $query->where('name', 'like', '%favorite%')
                    ->orWhere('name', 'like', '%favorit%')
                    ->orWhere('code', 'like', 'FAV-%');
            })
            ->update([
                'max_teams_per_school_mix' => 2,
            ]);
    }
};
