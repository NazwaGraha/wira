<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'title' => 'Juara Umum 1 Lomba Ketangkasan PMR Wira Se-Jawa Barat',
                'event_name' => 'Lomba Ketangkasan Palang Merah Wira Terbuka 2026',
                'organizer' => 'PMI Provinsi Jawa Barat & Forum Relawan Muda',
                'event_date' => '2026-02-28',
                'level' => 'Provinsi',
                'rank' => 'Juara Umum I',
                'category' => 'Umum',
                'contingent_members' => [
                    ['name' => 'Sulthan Arkana Putra R.', 'class_grade' => 'XI MIPA 1'],
                    ['name' => 'Nabila Putri Zahra', 'class_grade' => 'XI IPS 2'],
                    ['name' => 'Fadhil Pratama', 'class_grade' => 'X-3'],
                    ['name' => 'Zahra Aulia', 'class_grade' => 'XI MIPA 3'],
                    ['name' => 'Dimas Arya Kusuma', 'class_grade' => 'X-1'],
                ],
                'description' => 'Kontingen PMR Wira SMAN 1 Ciawi berhasil menyabet predikat Juara Umum I setelah mengumpulkan akumulasi medali tertinggi pada cabang Pertolongan Pertama Medis, Pasang Tandu Darurat, dan Cepat Tepat Kepalangmerahan bersaing dengan 64 kontingen sekolah se-Jawa Barat.',
                'trophy_image' => 'https://images.unsplash.com/photo-1578269174936-2709b6aeb913?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1526976668912-1a811878dd37?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=600&q=80',
                ],
                'is_featured' => true,
                'is_active' => true,
                'order_position' => 1,
            ],
            [
                'title' => 'Juara 1 Pertolongan Pertama Medis Darurat Nasional',
                'event_name' => 'National Youth Red Cross Medical First Aid Championship',
                'organizer' => 'KSR PMI Unit Perguruan Tinggi & PMI Pusat',
                'event_date' => '2026-01-15',
                'level' => 'Nasional',
                'rank' => 'Juara 1',
                'category' => 'Pertolongan Pertama',
                'contingent_members' => [
                    ['name' => 'Sulthan Arkana Putra R.', 'class_grade' => 'XI MIPA 1'],
                    ['name' => 'Rizky Ramadhan', 'class_grade' => 'XI MIPA 2'],
                    ['name' => 'Siti Fatimah', 'class_grade' => 'X-4'],
                    ['name' => 'Amanda Septiani', 'class_grade' => 'XI IPS 1'],
                ],
                'description' => 'Simulasi penanganan henti napas, resusitasi jantung paru (RJP), serta stabilisasi fraktur cervical dengan standar protokol medis darurat internasional.',
                'trophy_image' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1531058020387-3be344556be6?auto=format&fit=crop&w=600&q=80',
                ],
                'is_featured' => true,
                'is_active' => true,
                'order_position' => 2,
            ],
            [
                'title' => 'Juara 1 Pasang Bongkar Tandu Darurat & Evakuasi Cepat',
                'event_name' => 'Gebyar Relawan Muda Kabupaten Bogor 2025',
                'organizer' => 'PMI Kabupaten Bogor',
                'event_date' => '2025-11-10',
                'level' => 'Kabupaten/Kota',
                'rank' => 'Juara 1',
                'category' => 'Tandu Darurat',
                'contingent_members' => [
                    ['name' => 'Dimas Arya Kusuma', 'class_grade' => 'X-1'],
                    ['name' => 'M. Fauzan Pratama', 'class_grade' => 'X-2'],
                ],
                'description' => 'Mencatatkan rekor waktu 1 menit 18 detik perakitan tandu bambu lipat tali 8 dengan kekokohan dan uji kestabilan korban sempurna.',
                'trophy_image' => 'https://images.unsplash.com/photo-1589487391730-58f20eb2c308?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1528605248644-14dd04022da1?auto=format&fit=crop&w=600&q=80',
                ],
                'is_featured' => true,
                'is_active' => true,
                'order_position' => 3,
            ],
            [
                'title' => 'Juara 1 Cepat Tepat Kepalangmerahan & Hukum Humaniter',
                'event_name' => 'Lomba Cepat Tepat PMR Wira Se-Bogor Raya',
                'organizer' => 'SMAN 1 Kota Bogor & PMI Cabang Kota Bogor',
                'event_date' => '2025-08-20',
                'level' => 'Kabupaten/Kota',
                'rank' => 'Juara 1',
                'category' => 'Cepat Tepat',
                'contingent_members' => [
                    ['name' => 'Nabila Putri Zahra', 'class_grade' => 'XI IPS 2'],
                    ['name' => 'Fadhil Pratama', 'class_grade' => 'X-3'],
                    ['name' => 'Zahra Aulia', 'class_grade' => 'XI MIPA 3'],
                ],
                'description' => 'Menguasai materi 7 Prinsip Dasar Palang Merah, Hukum Humaniter Internasional, dan Sejarah Gerakan Palang Merah Sedunia.',
                'trophy_image' => 'https://images.unsplash.com/photo-1569517282132-25d22f4573e6?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [],
                'is_featured' => false,
                'is_active' => true,
                'order_position' => 4,
            ],
            [
                'title' => 'Juara 2 Mading Kreasi Edukasi Donor Darah & Sanitasi',
                'event_name' => 'Pekan Seni & Kreativitas Remaja Se-Jawa Barat',
                'organizer' => 'Dinas Pendidikan & PMI Provinsi Jawa Barat',
                'event_date' => '2025-05-18',
                'level' => 'Provinsi',
                'rank' => 'Juara 2',
                'category' => 'Mading Kreasi',
                'contingent_members' => [
                    ['name' => 'Annisa Rahmawati', 'class_grade' => 'XI MIPA 4'],
                    ['name' => 'Tiara Maharani', 'class_grade' => 'X-5'],
                ],
                'description' => 'Karya mading 3D interaktif bertema Ayo Donor Darah Sejak Dini dan PHBS Sekolah Sehat Bebas Penyakit.',
                'trophy_image' => 'https://images.unsplash.com/photo-1578269174936-2709b6aeb913?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [],
                'is_featured' => false,
                'is_active' => true,
                'order_position' => 5,
            ],
        ];

        foreach ($achievements as $ach) {
            Achievement::updateOrCreate(
                ['title' => $ach['title'], 'event_date' => $ach['event_date']],
                $ach
            );
        }
    }
}
