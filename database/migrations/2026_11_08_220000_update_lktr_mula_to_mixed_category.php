<?php

use App\Models\CompetitionCategory;
use App\Models\CompetitionParticipantTeam;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // 1. Update atau satukan Ketangkasan Tandu Reguler (LKTR-MULA) menjadi Kategori Umum / Campuran
            $paCategories = CompetitionCategory::where('level', 'Mula')
                ->where('name', 'like', '%Tandu%')
                ->where('gender_category', 'Putra')
                ->get();

            $piCategories = CompetitionCategory::where('level', 'Mula')
                ->where('name', 'like', '%Tandu%')
                ->where('gender_category', 'Putri')
                ->get();

            foreach ($paCategories as $paCat) {
                // Jadikan kategori LKTR-MULA sebagai Umum / Campuran
                $paCat->update([
                    'code' => 'LKTR-MULA',
                    'gender_category' => 'Umum',
                    'max_teams_per_school_mix' => 2,
                    'max_teams_per_school_pa' => 2,
                    'max_teams_per_school_pi' => 2,
                ]);

                // Cari pasangan PI untuk event yang sama
                $piCat = $piCategories->firstWhere('competition_event_id', $paCat->competition_event_id);
                if ($piCat) {
                    // Pindahkan tim dari PI ke LKTR-MULA
                    $piTeams = CompetitionParticipantTeam::where('competition_category_id', $piCat->id)->get();
                    foreach ($piTeams as $team) {
                        $existingCount = CompetitionParticipantTeam::where('competition_category_id', $paCat->id)
                            ->where('competition_registration_id', $team->competition_registration_id)
                            ->count();

                        $schoolName = $team->registration?->school_name ?: 'Kontingen';
                        $labelLetter = $existingCount === 0 ? 'A' : 'B';

                        $team->update([
                            'competition_category_id' => $paCat->id,
                            'team_name' => "{$schoolName} Regu {$labelLetter}",
                        ]);
                    }

                    // Hapus kategori PI lama
                    $piCat->delete();
                }
            }

            // Jika ada LKTR-MULA yang belum di-update
            CompetitionCategory::where('level', 'Mula')
                ->where('name', 'like', '%Tandu%')
                ->update([
                    'code' => 'LKTR-MULA',
                    'gender_category' => 'Umum',
                    'max_teams_per_school_mix' => 2,
                ]);

            // 2. Batasi Lomba Mewarnai maksimal 2 regu per sekolah
            CompetitionCategory::where('name', 'like', '%Mewarnai%')
                ->update([
                    'max_teams_per_school_mix' => 2,
                    'max_teams_per_school_pa' => 2,
                    'max_teams_per_school_pi' => 2,
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback
    }
};
