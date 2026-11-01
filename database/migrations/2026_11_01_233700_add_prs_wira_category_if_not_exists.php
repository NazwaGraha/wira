<?php

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $event = CompetitionEvent::first();
        if (! $event) {
            return;
        }

        $prs = CompetitionCategory::firstOrCreate(
            [
                'competition_event_id' => $event->id,
                'code' => 'PRS-WIRA',
            ],
            [
                'name' => 'Pendidikan Remaja Sebaya',
                'level' => 'Wira',
                'gender_category' => 'Umum',
                'scoring_type' => 'rubric',
                'is_photo_required' => false,
                'point_tier' => 'tier_1',
                'criteria_schema' => [
                    'Kreativitas',
                    'Kesesuaian Tema',
                    'Kejelasan Bahasa',
                    'Presentasi',
                    'Kecepatan Menjawab',
                ],
                'registration_fee' => 150000,
                'max_team_members' => 2,
                'max_quota' => 50,
                'max_teams_per_school_pa' => 2,
                'max_teams_per_school_pi' => 2,
                'max_teams_per_school_mix' => 2,
                'is_quota_enforced' => true,
                'has_rounds' => false,
                'order_position' => 21,
                'is_active' => true,
            ]
        );

        // Buat sekolah dan regu contoh tingkat Wira jika belum ada peserta terdaftar
        $teamCount = CompetitionParticipantTeam::where('competition_category_id', $prs->id)->count();
        if ($teamCount === 0) {
            $sampleSchools = [
                ['school' => 'SMAN 1 Ciawi (Bogor)', 'regu' => 'Regu Wira A', 'code' => 'PRS-WIR-01', 'no' => 1],
                ['school' => 'SMAN 2 Cibinong', 'regu' => 'Regu Wira B', 'code' => 'PRS-WIR-02', 'no' => 2],
                ['school' => 'SMKN 1 Ciomas', 'regu' => 'Regu Wira A', 'code' => 'PRS-WIR-03', 'no' => 3],
                ['school' => 'SMA Bina Bangsa', 'regu' => 'Regu Wira A', 'code' => 'PRS-WIR-04', 'no' => 4],
                ['school' => 'SMAN 1 Megamendung', 'regu' => 'Regu Wira A', 'code' => 'PRS-WIR-05', 'no' => 5],
            ];

            foreach ($sampleSchools as $idx => $s) {
                $reg = CompetitionRegistration::firstOrCreate(
                    [
                        'competition_event_id' => $event->id,
                        'school_name' => $s['school'],
                        'level' => 'Wira',
                    ],
                    [
                        'registration_code' => 'SBB-PRSWIR-'.strtoupper(substr(md5($s['school']), 0, 5)),
                        'advisor_name' => 'Pembina PMR '.$s['school'],
                        'advisor_phone' => '08129876543'.$idx,
                        'advisor_email' => 'pembinawira'.($idx + 1).'@pmrwirasmanic.or.id',
                        'school_address' => 'Kabupaten / Kota Bogor',
                        'total_payment' => 150000,
                        'registration_wave' => 1,
                        'status' => 'verified',
                        'verified_at' => now(),
                        'verified_by' => 'Panitia PMR Wira SMAN 1 Ciawi',
                    ]
                );

                CompetitionParticipantTeam::firstOrCreate(
                    [
                        'competition_category_id' => $prs->id,
                        'competition_registration_id' => $reg->id,
                        'team_name' => $s['regu'],
                    ],
                    [
                        'team_label' => $s['school'],
                        'order_number' => $s['no'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prs = CompetitionCategory::where('code', 'PRS-WIRA')->first();
        if ($prs) {
            CompetitionParticipantTeam::where('competition_category_id', $prs->id)->delete();
            $prs->delete();
        }
    }
};
