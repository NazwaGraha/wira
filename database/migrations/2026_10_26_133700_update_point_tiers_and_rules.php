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
        // Tier 1 (12, 10, 8, 6, 4 Poin):
        // LPP, LCT, Lomba Pendidikan Remaja Sebaya, dan Olimpiade Kepalangmerahan
        DB::table('competition_categories')
            ->where(function ($q) {
                $q->where('name', 'like', '%Pertolongan Pertama%')
                    ->orWhere('name', 'like', '%Cepat Tepat%')
                    ->orWhere('name', 'like', '%Olimpiade%')
                    ->orWhere('name', 'like', '%Pendidikan Remaja Sebaya%')
                    ->orWhere('name', 'like', '%PRS%')
                    ->orWhere('code', 'like', 'LPP-%')
                    ->orWhere('code', 'like', 'LCT-%')
                    ->orWhere('code', 'like', 'OLIM-%')
                    ->orWhere('code', 'like', 'PRS-%');
            })
            ->update(['point_tier' => 'tier_1']);

        // Tier 2 (10, 8, 6, 4, 2 Poin):
        // LKTR, LKCT, dan Lomba Mewarnai
        DB::table('competition_categories')
            ->where(function ($q) {
                $q->where('name', 'like', '%Tandu%')
                    ->orWhere('name', 'like', '%Cuci Tangan%')
                    ->orWhere('name', 'like', '%Mewarnai%')
                    ->orWhere('code', 'like', 'LKTR-%')
                    ->orWhere('code', 'like', 'LKCT-%')
                    ->orWhere('code', 'like', 'MEWARNAI-%');
            })
            ->update(['point_tier' => 'tier_2']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive schema change
    }
};
