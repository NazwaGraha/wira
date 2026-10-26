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
        // 1. Pertolongan Pertama (Semua tingkatan Mula, Madya, Wira):
        // Hapus nilai tertulis & waktu tertulis, hanya Nilai Praktik dan Waktu Praktik
        DB::table('competition_categories')
            ->where('name', 'like', '%Pertolongan Pertama%')
            ->orWhere('code', 'like', 'LPP-%')
            ->update([
                'scoring_type' => 'practical_time',
                'criteria_schema' => json_encode(['Nilai Praktik', 'Waktu Praktik']),
            ]);

        // 2. Olimpiade Kepalangmerahan:
        // Hapus nilai praktik & waktu praktik, hanya Nilai Tertulis dan Waktu Tertulis
        DB::table('competition_categories')
            ->where('name', 'like', '%Olimpiade%')
            ->orWhere('code', 'like', 'OLIM-%')
            ->update([
                'scoring_type' => 'written_time',
                'criteria_schema' => json_encode(['Nilai Tertulis', 'Waktu Tertulis']),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Pertolongan Pertama
        DB::table('competition_categories')
            ->where(function ($q) {
                $q->where('name', 'like', '%Pertolongan Pertama%')
                    ->orWhere('code', 'like', 'LPP-%');
            })
            ->whereIn('level', ['Madya', 'Wira'])
            ->update([
                'scoring_type' => 'written_practical_time',
                'criteria_schema' => json_encode(['Nilai Tertulis', 'Waktu Tertulis', 'Nilai Praktik', 'Waktu Praktik']),
            ]);

        DB::table('competition_categories')
            ->where(function ($q) {
                $q->where('name', 'like', '%Pertolongan Pertama%')
                    ->orWhere('code', 'like', 'LPP-%');
            })
            ->where('level', 'Mula')
            ->update([
                'scoring_type' => 'standard_time',
                'criteria_schema' => json_encode(['Nilai', 'Waktu']),
            ]);

        // Revert Olimpiade Kepalangmerahan
        DB::table('competition_categories')
            ->where('name', 'like', '%Olimpiade%')
            ->orWhere('code', 'like', 'OLIM-%')
            ->update([
                'scoring_type' => 'standard_time',
                'criteria_schema' => json_encode(['Nilai', 'Waktu']),
            ]);
    }
};
