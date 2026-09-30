<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Article;
use App\Models\Activity;
use App\Models\BloodStock;
use App\Models\MemberRegistration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@pmrwirasman1c.sch.id'],
            [
                'name' => 'Admin PMR Wira SMAN 1 Ciawi',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Categories
        $categories = [
            [
                'name' => 'Tips & Pertolongan Pertama',
                'slug' => 'tips-pertolongan-pertama',
                'color' => '#dc2626',
                'description' => 'Panduan dan protokol tindakan medis dasar untuk siswa sekolah.',
            ],
            [
                'name' => 'Edukasi Kesehatan',
                'slug' => 'edukasi-kesehatan',
                'color' => '#2563eb',
                'description' => 'Edukasi pola hidup bersih, gizi seimbang, dan kesehatan remaja.',
            ],
            [
                'name' => 'Kesiapsiagaan Bencana',
                'slug' => 'kesiapsiagaan-bencana',
                'color' => '#ea580c',
                'description' => 'Mitigasi gempa, kebakaran, dan evakuasi mandiri di lingkungan sekolah.',
            ],
            [
                'name' => 'Donor Darah',
                'slug' => 'donor-darah',
                'color' => '#980000',
                'description' => 'Info seputar kegiatan donor darah sukarela dan manfaat transfusi darah.',
            ],
            [
                'name' => 'Kisah Relawan',
                'slug' => 'kisah-relawan',
                'color' => '#059669',
                'description' => 'Catatan perjuangan, inspirasi, dan bakti siswa PMR SMAN 1 Ciawi.',
            ],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['slug']] = Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 3. Articles (matching mockup)
        $articles = [
            [
                'title' => 'Panduan Penanganan Awal Cedera dan Pingsan Saat Upacara Sekolah',
                'slug' => 'panduan-penanganan-awal-cedera-pingsan-upacara',
                'category_id' => $catModels['tips-pertolongan-pertama']->id,
                'excerpt' => 'Langkah-langkah cepat pertolongan pertama bagi anggota PMR ketika mendapati rekan siswa yang kelelahan atau pingsan saat upacara bendera.',
                'body' => "Pingsan (sinkop) adalah hilangnya kesadaran sementara akibat berkurangnya pasokan oksigen ke otak. Kondisi ini sering terjadi saat upacara bendera di lapangan sekolah karena panas terik dan belum sarapan.\n\n### Langkah Pertolongan Pertama:\n1. **Pindahkan ke Tempat Teduh dan Nyaman**: Bawa korban ke tenda posko PMR atau ruang UKS dengan sirkulasi udara baik.\n2. **Baringkan dan Tinggikan Kaki**: Angkat kaki setinggi 20-30 cm untuk melancarkan aliran darah kembali ke otak.\n3. **Longgarkan Pakaian**: Buka kancing kerah baju, dasi, dan ikat pinggang.\n4. **Cek Respons dan Pernapasan**: Berikan rangsangan wewangian seperti minyak kayu putih bila responsif.\n5. **Beri Minum Hangat dan Manis**: Hanya saat korban sudah sepenuhnya sadar dan mampu menelan dengan baik.\n\nDengan kesigapan relawan PMR Wira, penanganan di lapangan dapat berjalan optimal dan mencegah komplikasi lebih lanjut.",
                'thumbnail' => '/mockups/03_kegiatan.jpg',
                'author_name' => 'Tim Medis PMR SMAN 1 Ciawi',
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 1420,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Edukasi Kesehatan Remaja: Pencegahan Anemia untuk Siswi SMA',
                'slug' => 'edukasi-kesehatan-pencegahan-anemia-remaja',
                'category_id' => $catModels['edukasi-kesehatan']->id,
                'excerpt' => 'Pentingnya asupan zat besi dan tablet tambah darah bagi remaja putri dalam mendukung konsentrasi belajar dan stamina harian.',
                'body' => "Anemia adalah kondisi di mana kadar hemoglobin (Hb) dalam darah berada di bawah normal. Di kalangan remaja putri, risiko anemia meningkat akibat siklus menstruasi bulanan dan pola diet yang kurang seimbang.\n\nProgram PMR Wira SMAN 1 Ciawi bekerja sama dengan Puskesmas Ciawi rutin membagikan Tablet Tambah Darah (TTD) serta sosialisasi gizi 'Isi Piringku'. Mari jaga kesehatan generasi muda sejak dini!",
                'thumbnail' => '/mockups/07_artikel.jpg',
                'author_name' => 'Sie Kesehatan PMR',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 840,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Simulasi Mitigasi Gempa Bumi: Sekolah Tangguh Bencana di SMAN 1 Ciawi',
                'slug' => 'simulasi-mitigasi-gempa-bumi-sekolah-tangguh-bencana',
                'category_id' => $catModels['kesiapsiagaan-bencana']->id,
                'excerpt' => "Dokumentasi latihan evakuasi mandiri 'Drop, Cover, and Hold On' bersama seluruh dewan guru dan perwakilan kelas.",
                'body' => "Kesiapsiagaan bencana bukanlah opsi, melainkan kebutuhan mendasar. Sebagai bagian dari program Sekolah Aman Bencana, PMR Wira SMAN 1 Ciawi memimpin simulasi jalur evakuasi menuju titik kumpul lapangan utama.\n\nDalam simulasi ini, relawan melatih teknik triage darurat dan evakuasi tandu darurat dengan respon waktu di bawah 4 menit.",
                'thumbnail' => '/mockups/03_kegiatan.jpg',
                'author_name' => 'Divisi Siaga Bencana',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 610,
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Kisah Relawan: Pengalaman Berharga di Jumpa Bakti Gembira (Jumbara) 2026',
                'slug' => 'kisah-relawan-jumbara-pmr-2026',
                'category_id' => $catModels['kisah-relawan']->id,
                'excerpt' => 'Refleksi kebersamaan, kepemimpinan, dan persaudaraan tanpa batas selama kemah akbar PMR Wira se-Jawa Barat.',
                'body' => "Jumbara mengajarkan kami bahwa kemanusiaan adalah jembatan yang menghubungkan semua perbedaan. Bertemu dengan ratusan relawan muda dari berbagai kabupaten memberi inspirasi bahwa aksi kecil kita memiliki dampak nyata bagi sesama.",
                'thumbnail' => '/mockups/04_galeri.jpg',
                'author_name' => 'Muhammad Rizky (Ketua Umum)',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 975,
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Mitos dan Fakta Donor Darah: Kenapa Tak Perlu Takut Jarum Suntik',
                'slug' => 'mitos-dan-fakta-donor-darah-remaja',
                'category_id' => $catModels['donor-darah']->id,
                'excerpt' => 'Mengupas tuntas ketakutan umum seputar donor darah: apakah bikin gemuk, apakah sakit, dan siapa saja yang boleh berpartisipasi.',
                'body' => "Banyak siswa yang bertanya: 'Apakah donor darah bikin lemas selamanya?' Jawabannya adalah tidak! Tubuh kita akan secara alami memproduksi sel-sel darah baru yang segar dalam hitungan minggu. Donor darah justru membantu regenerasi sel dan menjaga kesehatan kardiovaskular.",
                'thumbnail' => '/mockups/05_donor_darah.jpg',
                'author_name' => 'Unit Donor Darah PMR',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 1120,
                'published_at' => now()->subDays(15),
            ],
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(['slug' => $art['slug']], array_merge($art, ['user_id' => $admin->id]));
        }

        // 4. Activities (matching mockup)
        $activities = [
            [
                'title' => 'Pelatihan Pertolongan Pertama (PP) Tingkat Wira 2026',
                'slug' => 'pelatihan-pertolongan-pertama-2026',
                'category' => 'Pertolongan Pertama',
                'event_date' => now()->addDays(5),
                'location' => 'Laboratorium Biologi & Lapangan SMAN 1 Ciawi',
                'description' => 'Pelatihan intensif pembalutan, pembidaian, RJP/CPR, dan evakuasi korban oleh fasilitator PMI Kabupaten Bogor.',
                'image' => '/mockups/03_kegiatan.jpg',
                'is_featured' => true,
            ],
            [
                'title' => 'Aksi Donor Darah Sukarela SMAN 1 Ciawi Bersama UDD PMI',
                'slug' => 'donor-darah-sekolah-2026',
                'category' => 'Donor Darah',
                'event_date' => now()->addDays(14),
                'location' => 'Aula Serbaguna SMAN 1 Ciawi',
                'description' => 'Bakti sosial donor darah terbuka untuk dewan guru, alumni, orang tua murid, dan siswa kelas XII yang telah berusia 17 tahun.',
                'image' => '/mockups/05_donor_darah.jpg',
                'is_featured' => true,
            ],
            [
                'title' => 'Simulasi Bencana dan Sekolah Aman Bencana',
                'slug' => 'simulasi-bencana-sekolah-2026',
                'category' => 'Kesiapsiagaan Bencana',
                'event_date' => now()->subDays(10),
                'location' => 'Kompleks SMAN 1 Ciawi',
                'description' => 'Uji prototipe alarm gempa dan rute evakuasi cepat ke zona aman lapangan basket.',
                'image' => '/mockups/03_kegiatan.jpg',
                'is_featured' => false,
            ],
            [
                'title' => 'Jumpa Bakti Gembira (JUMBARA) PMR Tingkat Kabupaten',
                'slug' => 'jumbara-pmr-wira-2026',
                'category' => 'Pendidikan',
                'event_date' => now()->subMonths(1),
                'location' => 'Bumi Perkemahan Mandapa',
                'description' => 'Kontingen SMAN 1 Ciawi meraih predikat Unit Terbaik Bidang Pertolongan Pertama dan Sanitasi Kesehatan.',
                'image' => '/mockups/04_galeri.jpg',
                'is_featured' => false,
            ],
            [
                'title' => 'Bakti Sosial & Pemeriksaan Kesehatan Gratis Warga Sekitar',
                'slug' => 'bakti-sosial-warga-ciawi',
                'category' => 'Bakti Sosial',
                'event_date' => now()->subMonths(2),
                'location' => 'Desa Ciawi, Bogor',
                'description' => 'Pemeriksaan tekanan darah, gula darah sewaktu, dan pembagian paket sembako peduli sesama.',
                'image' => '/mockups/03_kegiatan.jpg',
                'is_featured' => false,
            ],
        ];

        foreach ($activities as $act) {
            Activity::updateOrCreate(['slug' => $act['slug']], $act);
        }

        // 5. Blood Stocks
        $stocks = [
            ['blood_type' => 'A+', 'status' => 'aman', 'bags_count' => 45, 'notes' => 'Stok mencukupi untuk kebutuhan harian'],
            ['blood_type' => 'B+', 'status' => 'menipis', 'bags_count' => 12, 'notes' => 'Diperlukan donor tambahan minggu ini'],
            ['blood_type' => 'AB+', 'status' => 'aman', 'bags_count' => 28, 'notes' => 'Tersedia di cold storage UDD'],
            ['blood_type' => 'O+', 'status' => 'kritis', 'bags_count' => 6, 'notes' => 'Sangat dibutuhkan, donor segera!'],
        ];

        foreach ($stocks as $stock) {
            BloodStock::updateOrCreate(['blood_type' => $stock['blood_type']], $stock);
        }

        // 6. Sample Member Registration
        MemberRegistration::updateOrCreate(
            ['phone' => '081234567890'],
            [
                'full_name' => 'Aisyah Putri Rahmadhani',
                'class_grade' => 'X-MIPA 2',
                'nisn' => '0087654321',
                'email' => 'aisyah.putri@gmail.com',
                'interest_field' => 'Pertolongan Pertama (PP)',
                'motivation' => 'Ingin memperdalam ilmu medis dasar dan melatih jiwa kepedulian sosial terhadap sesama teman dan masyarakat.',
                'status' => 'verified',
            ]
        );

        // 7. Hero Slides Slider
        $slides = [
            [
                'title' => 'Ragana Dwi Pantara 2026/2027',
                'image_path' => '/mockups/01_home.jpg',
                'caption' => 'Semangat kepemimpinan dan dedikasi relawan muda SMAN 1 Ciawi.',
                'order_position' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Pelatihan Pertolongan Pertama Intensif',
                'image_path' => '/mockups/03_kegiatan.jpg',
                'caption' => 'Membentuk kader medis sekolah yang sigap, tangkas, dan terlatih.',
                'order_position' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Aksi Donor Darah Sukarela Sekolah',
                'image_path' => '/mockups/05_donor_darah.jpg',
                'caption' => 'Satu kantong darah Anda, sejuta harapan bagi sesama.',
                'order_position' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Kebersamaan & Gelar Kreasi Kemanusiaan',
                'image_path' => '/mockups/04_galeri.jpg',
                'caption' => 'Menyatu dalam persaudaraan tanpa batas di bawah panji PMI.',
                'order_position' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $slide) {
            \App\Models\HeroSlide::updateOrCreate(['title' => $slide['title']], $slide);
        }

        // 8. Organization Structure Settings & Members
        \App\Models\OrganizationSetting::updateOrCreate(
            ['id' => 1],
            [
                'badge' => 'Bagan Kepengurusan',
                'title' => 'Struktur Organisasi 2026/2027',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara — Sinergi kepemimpinan dan dedikasi relawan siswa.',
            ]
        );

        $orgMembers = [
            [
                'position' => 'Pembina PMR',
                'name' => 'Drs. H. Mulyadi, M.Pd',
                'subtitle' => 'Pembina Ekstrakurikuler',
                'level' => 1,
                'order_position' => 1,
                'icon' => 'fa-solid fa-user-tie',
            ],
            [
                'position' => 'Ketua Umum 2026/2027',
                'name' => 'Muhammad Rizky Pratama',
                'subtitle' => 'Kelas XI-MIPA 1',
                'level' => 2,
                'order_position' => 1,
                'icon' => 'fa-solid fa-user-shield',
            ],
            [
                'position' => 'Sekretaris',
                'name' => 'Siti Nurhaliza',
                'subtitle' => 'Administrasi & Surat',
                'level' => 3,
                'order_position' => 1,
                'icon' => 'fa-solid fa-file-signature',
            ],
            [
                'position' => 'Bendahara',
                'name' => 'Farhan Ramadhan',
                'subtitle' => 'Keuangan & Kas',
                'level' => 3,
                'order_position' => 2,
                'icon' => 'fa-solid fa-wallet',
            ],
            [
                'position' => 'Sie Kesehatan & P3K',
                'name' => 'Ahmad Zulfikar',
                'subtitle' => 'Piket UKS & Tim Medis',
                'level' => 4,
                'order_position' => 1,
                'icon' => 'fa-solid fa-notes-medical',
            ],
            [
                'position' => 'Sie Kegiatan & Diklat',
                'name' => 'Nabila Zahra',
                'subtitle' => 'Pelatihan & Latihan Gabungan',
                'level' => 4,
                'order_position' => 2,
                'icon' => 'fa-solid fa-calendar-days',
            ],
            [
                'position' => 'Sie Humas & Publikasi',
                'name' => 'Dimas Arya',
                'subtitle' => 'Media Sosial & Dokumentasi',
                'level' => 4,
                'order_position' => 3,
                'icon' => 'fa-solid fa-bullhorn',
            ],
        ];

        foreach ($orgMembers as $member) {
            \App\Models\OrganizationMember::updateOrCreate(
                ['position' => $member['position'], 'name' => $member['name']],
                $member
            );
        }
    }
}
