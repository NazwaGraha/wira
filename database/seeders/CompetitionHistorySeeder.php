<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetitionEvent;

class CompetitionHistorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Edisi I (Tahun 2023)
        CompetitionEvent::firstOrCreate(
            ['slug' => 'sua-bhakti-berkarya-i-2023'],
            [
                'title' => 'SUA BHAKTI BERKARYA I TAHUN 2023',
                'theme' => 'Tumbuhkan Semangat Kemanusiaan dan Solidaritas Relawan Muda',
                'start_date' => '2023-10-14',
                'end_date' => '2023-10-15',
                'location' => 'Kampus SMAN 1 Ciawi Bogor',
                'description' => 'Penyelenggaraan perdana ajang kompetisi kepalangmerahan PMR tingkat Mula (SD), Madya (SMP), dan Wira (SMA) se-Bogor dan sekitarnya.',
                'registration_fee' => 100000,
                'bank_name' => 'Bank BCA',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'PMR WIRA SMAN 1 CIAWI',
                'is_active' => false,
                'is_registration_open' => false,
            ]
        );

        // 2. Edisi II (Tahun 2024)
        CompetitionEvent::firstOrCreate(
            ['slug' => 'sua-bhakti-berkarya-ii-2024'],
            [
                'title' => 'SUA BHAKTI BERKARYA II TAHUN 2024',
                'theme' => 'Membangun Generasi Muda Palang Merah yang Tanggap, Tangkas, dan Berkarakter',
                'start_date' => '2024-10-19',
                'end_date' => '2024-10-20',
                'location' => 'Kampus SMAN 1 Ciawi Bogor',
                'description' => 'Edisi kedua kompetisi kepalangmerahan PMR tingkat Mula, Madya, dan Wira se-Jabodetabek dan sekitarnya.',
                'registration_fee' => 125000,
                'bank_name' => 'Bank BCA',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'PMR WIRA SMAN 1 CIAWI',
                'is_active' => false,
                'is_registration_open' => false,
            ]
        );
    }
}
