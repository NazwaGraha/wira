<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetitionEvent;
use App\Models\CompetitionCategory;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionScore;

class CompetitionSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $event = CompetitionEvent::where('slug', 'sua-bhakti-berkarya-iii-2025')->first();
        if (!$event) return;

        // Sample Registration 1: MTSN Kota Bogor (Madya)
        $reg1 = CompetitionRegistration::updateOrCreate(
            ['registration_code' => 'SBB-MTSN01'],
            [
                'competition_event_id' => $event->id,
                'school_name' => 'MTSN KOTA BOGOR',
                'level' => 'Madya',
                'advisor_name' => 'Drs. H. Ahmad Fauzi',
                'advisor_phone' => '081234567891',
                'total_payment' => 600000,
                'status' => 'verified',
                'verified_at' => now(),
            ]
        );

        // Sample Registration 2: SMPN 2 Cigombong (Madya)
        $reg2 = CompetitionRegistration::updateOrCreate(
            ['registration_code' => 'SBB-SMPN02'],
            [
                'competition_event_id' => $event->id,
                'school_name' => 'SMPN 2 CIGOMBONG',
                'level' => 'Madya',
                'advisor_name' => 'Siti Rahmawati, S.Pd',
                'advisor_phone' => '081398765432',
                'total_payment' => 450000,
                'status' => 'verified',
                'verified_at' => now(),
            ]
        );

        // Sample Registration 3: SMPN 1 Ciawi (Madya)
        $reg3 = CompetitionRegistration::updateOrCreate(
            ['registration_code' => 'SBB-SMPN01'],
            [
                'competition_event_id' => $event->id,
                'school_name' => 'SMPN 1 CIAWI',
                'level' => 'Madya',
                'advisor_name' => 'Budi Pratama, S.Pd',
                'advisor_phone' => '085712345678',
                'total_payment' => 450000,
                'status' => 'verified',
                'verified_at' => now(),
            ]
        );

        // Category: LPP Madya Putra
        $lppPa = CompetitionCategory::where('code', 'LPP-MADYA-PA')->first();
        if ($lppPa) {
            $team1 = CompetitionParticipantTeam::updateOrCreate(
                ['competition_registration_id' => $reg1->id, 'competition_category_id' => $lppPa->id, 'team_label' => '(A)'],
                ['order_number' => '3.1', 'team_name' => 'MTSN KOTA BOGOR (A)']
            );
            CompetitionScore::updateOrCreate(
                ['competition_category_id' => $lppPa->id, 'competition_participant_team_id' => $team1->id],
                [
                    'round_name' => 'Utama',
                    'score_details' => ['written_score' => 61, 'written_time' => '25.02', 'practical_score' => 960, 'practical_time' => '6.30'],
                    'final_score' => 83.75,
                    'time_recorded' => '6.30',
                    'rank' => 1,
                ]
            );

            $team2 = CompetitionParticipantTeam::updateOrCreate(
                ['competition_registration_id' => $reg1->id, 'competition_category_id' => $lppPa->id, 'team_label' => '(B)'],
                ['order_number' => '3.2', 'team_name' => 'MTSN KOTA BOGOR (B)']
            );
            CompetitionScore::updateOrCreate(
                ['competition_category_id' => $lppPa->id, 'competition_participant_team_id' => $team2->id],
                [
                    'round_name' => 'Utama',
                    'score_details' => ['written_score' => 68, 'written_time' => '23.32', 'practical_score' => 890, 'practical_time' => '6.42'],
                    'final_score' => 81.65,
                    'time_recorded' => '6.42',
                    'rank' => 2,
                ]
            );

            $team3 = CompetitionParticipantTeam::updateOrCreate(
                ['competition_registration_id' => $reg3->id, 'competition_category_id' => $lppPa->id, 'team_label' => ''],
                ['order_number' => '7', 'team_name' => 'SMPN 1 CIAWI']
            );
            CompetitionScore::updateOrCreate(
                ['competition_category_id' => $lppPa->id, 'competition_participant_team_id' => $team3->id],
                [
                    'round_name' => 'Utama',
                    'score_details' => ['written_score' => 70, 'written_time' => '27.54', 'practical_score' => 800, 'practical_time' => '7.00'],
                    'final_score' => 76.50,
                    'time_recorded' => '7.00',
                    'rank' => 3,
                ]
            );
        }

        // Category: LPP Madya Putri
        $lppPi = CompetitionCategory::where('code', 'LPP-MADYA-PI')->first();
        if ($lppPi) {
            $teamA = CompetitionParticipantTeam::updateOrCreate(
                ['competition_registration_id' => $reg2->id, 'competition_category_id' => $lppPi->id],
                ['order_number' => '1', 'team_name' => 'SMPN 2 CIGOMBONG']
            );
            CompetitionScore::updateOrCreate(
                ['competition_category_id' => $lppPi->id, 'competition_participant_team_id' => $teamA->id],
                [
                    'round_name' => 'Utama',
                    'score_details' => ['written_score' => 73, 'written_time' => '20.09', 'practical_score' => 950, 'practical_time' => '4.19'],
                    'final_score' => 87.30,
                    'time_recorded' => '4.19',
                    'rank' => 1,
                ]
            );
        }
    }
}
