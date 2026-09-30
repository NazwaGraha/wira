<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetitionEvent;
use App\Models\CompetitionCategory;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $event = CompetitionEvent::firstOrCreate(
            ['slug' => 'sua-bhakti-berkarya-iii-2025'],
            [
                'title' => 'SUA BHAKTI BERKARYA III TAHUN 2025',
                'theme' => 'Tingkatkan Jiwa Kemanusiaan dan Sportivitas Relawan Muda',
                'start_date' => '2026-10-15',
                'end_date' => '2026-10-16',
                'location' => 'Kampus SMAN 1 Ciawi Bogor',
                'description' => 'Ajang kompetisi kepalangmerahan bergengsi tingkat Mula (SD), Madya (SMP), dan Wira (SMA/SMK/MA) se-Jabodetabek dan sekitarnya.',
                'registration_fee' => 150000,
                'bank_name' => 'Bank BCA',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'PMR WIRA SMAN 1 CIAWI',
                'is_active' => true,
                'is_registration_open' => true,
            ]
        );

        $categories = [
            // === MULA (SD) ===
            [
                'code' => 'LPP-MULA-PA',
                'name' => 'Pertolongan Pertama',
                'level' => 'Mula',
                'gender_category' => 'Putra',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Nilai', 'Waktu'],
                'order_position' => 1,
            ],
            [
                'code' => 'LPP-MULA-PI',
                'name' => 'Pertolongan Pertama',
                'level' => 'Mula',
                'gender_category' => 'Putri',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Nilai', 'Waktu'],
                'order_position' => 2,
            ],
            [
                'code' => 'LKTR-MULA',
                'name' => 'Ketangkasan Tandu Reguler',
                'level' => 'Mula',
                'gender_category' => 'Umum',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_2',
                'criteria_schema' => ['Nilai Tandu', 'Waktu'],
                'order_position' => 3,
            ],
            [
                'code' => 'LKCT-MULA',
                'name' => 'Ketangkasan Cuci Tangan',
                'level' => 'Mula',
                'gender_category' => 'Umum',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_2',
                'criteria_schema' => ['Nilai'],
                'order_position' => 4,
            ],
            [
                'code' => 'MEWARNAI-MULA',
                'name' => 'Lomba Mewarnai',
                'level' => 'Mula',
                'gender_category' => 'Umum',
                'scoring_type' => 'multi_criteria',
                'point_tier' => 'tier_2',
                'criteria_schema' => ['Kerapihan dan Kebersihan', 'Kombinasi Warna dan Estetika', 'Kreativitas'],
                'order_position' => 5,
            ],
            [
                'code' => 'MADING-MULA',
                'name' => 'Mading Kreasi',
                'level' => 'Mula',
                'gender_category' => 'Umum',
                'scoring_type' => 'multi_criteria',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Kreativitas', 'Presentasi'],
                'order_position' => 6,
            ],

            // === MADYA (SMP) ===
            [
                'code' => 'LPP-MADYA-PA',
                'name' => 'Pertolongan Pertama',
                'level' => 'Madya',
                'gender_category' => 'Putra',
                'scoring_type' => 'written_practical_time',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Nilai Tertulis', 'Waktu Tertulis', 'Nilai Praktik', 'Waktu Praktik'],
                'order_position' => 7,
            ],
            [
                'code' => 'LPP-MADYA-PI',
                'name' => 'Pertolongan Pertama',
                'level' => 'Madya',
                'gender_category' => 'Putri',
                'scoring_type' => 'written_practical_time',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Nilai Tertulis', 'Waktu Tertulis', 'Nilai Praktik', 'Waktu Praktik'],
                'order_position' => 8,
            ],
            [
                'code' => 'LKTR-MADYA-PA',
                'name' => 'Ketangkasan Tandu Reguler',
                'level' => 'Madya',
                'gender_category' => 'Putra',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_2',
                'criteria_schema' => ['Nilai', 'Waktu'],
                'order_position' => 9,
            ],
            [
                'code' => 'LKTR-MADYA-PI',
                'name' => 'Ketangkasan Tandu Reguler',
                'level' => 'Madya',
                'gender_category' => 'Putri',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_2',
                'criteria_schema' => ['Nilai', 'Waktu'],
                'order_position' => 10,
            ],
            [
                'code' => 'LCT-MADYA',
                'name' => 'Cepat Tepat',
                'level' => 'Madya',
                'gender_category' => 'Umum',
                'scoring_type' => 'bracket_quiz',
                'point_tier' => 'tier_1',
                'has_rounds' => true,
                'criteria_schema' => ['Nilai Babak'],
                'order_position' => 11,
            ],
            [
                'code' => 'OLIM-MADYA',
                'name' => 'Olimpiade Kepalangmerahan',
                'level' => 'Madya',
                'gender_category' => 'Umum',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Nilai', 'Waktu'],
                'order_position' => 12,
            ],
            [
                'code' => 'MADING-MADYA',
                'name' => 'Mading Kreasi',
                'level' => 'Madya',
                'gender_category' => 'Umum',
                'scoring_type' => 'multi_criteria',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Kreativitas', 'Presentasi'],
                'order_position' => 13,
            ],
            [
                'code' => 'FAV-MADYA',
                'name' => 'PMR Favorite',
                'level' => 'Madya',
                'gender_category' => 'Umum',
                'scoring_type' => 'social_engagement',
                'point_tier' => 'tier_3',
                'criteria_schema' => ['Like', 'Sticker'],
                'order_position' => 14,
            ],

            // === WIRA (SMA) ===
            [
                'code' => 'LPP-WIRA-PA',
                'name' => 'Pertolongan Pertama',
                'level' => 'Wira',
                'gender_category' => 'Putra',
                'scoring_type' => 'written_practical_time',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Nilai Tertulis', 'Waktu Tertulis', 'Nilai Praktik', 'Waktu Praktik'],
                'order_position' => 15,
            ],
            [
                'code' => 'LPP-WIRA-PI',
                'name' => 'Pertolongan Pertama',
                'level' => 'Wira',
                'gender_category' => 'Putri',
                'scoring_type' => 'written_practical_time',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Nilai Tertulis', 'Waktu Tertulis', 'Nilai Praktik', 'Waktu Praktik'],
                'order_position' => 16,
            ],
            [
                'code' => 'LKTR-WIRA-PA',
                'name' => 'Ketangkasan Tandu Reguler',
                'level' => 'Wira',
                'gender_category' => 'Putra',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_2',
                'criteria_schema' => ['Nilai', 'Waktu'],
                'order_position' => 17,
            ],
            [
                'code' => 'LKTR-WIRA-PI',
                'name' => 'Ketangkasan Tandu Reguler',
                'level' => 'Wira',
                'gender_category' => 'Putri',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_2',
                'criteria_schema' => ['Nilai', 'Waktu'],
                'order_position' => 18,
            ],
            [
                'code' => 'LCT-WIRA',
                'name' => 'Cepat Tepat',
                'level' => 'Wira',
                'gender_category' => 'Umum',
                'scoring_type' => 'bracket_quiz',
                'point_tier' => 'tier_1',
                'has_rounds' => true,
                'criteria_schema' => ['Nilai Babak'],
                'order_position' => 19,
            ],
            [
                'code' => 'OLIM-WIRA',
                'name' => 'Olimpiade Kepalangmerahan',
                'level' => 'Wira',
                'gender_category' => 'Umum',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Nilai', 'Waktu'],
                'order_position' => 20,
            ],
            [
                'code' => 'MADING-WIRA',
                'name' => 'Mading Kreasi',
                'level' => 'Wira',
                'gender_category' => 'Umum',
                'scoring_type' => 'multi_criteria',
                'point_tier' => 'tier_1',
                'criteria_schema' => ['Kreativitas', 'Presentasi'],
                'order_position' => 21,
            ],
            [
                'code' => 'FAV-WIRA',
                'name' => 'PMR Favorite',
                'level' => 'Wira',
                'gender_category' => 'Umum',
                'scoring_type' => 'social_engagement',
                'point_tier' => 'tier_3',
                'criteria_schema' => ['Like', 'Sticker'],
                'order_position' => 22,
            ],
        ];

        foreach ($categories as $cat) {
            CompetitionCategory::updateOrCreate(
                [
                    'competition_event_id' => $event->id,
                    'code' => $cat['code']
                ],
                array_merge($cat, ['competition_event_id' => $event->id])
            );
        }
    }
}
