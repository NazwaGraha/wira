<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Article;
use App\Models\BloodStock;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Member;
use App\Models\MemberRegistration;
use App\Models\OrganizationGuideline;
use App\Models\OrganizationMember;
use App\Models\OrganizationSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
                'body' => 'Jumbara mengajarkan kami bahwa kemanusiaan adalah jembatan yang menghubungkan semua perbedaan. Bertemu dengan ratusan relawan muda dari berbagai kabupaten memberi inspirasi bahwa aksi kecil kita memiliki dampak nyata bagi sesama.',
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
            HeroSlide::updateOrCreate(['title' => $slide['title']], $slide);
        }

        // 8. Database Members (Daftar Anggota PMR)
        Member::truncate();

        $membersData = [
            [
                'nis' => '242510001',
                'name' => 'Abdul Hamid Saputra, S.Hum., M.Pd.',
                'position' => 'Pembina PMR',
                'class_grade' => 'Guru Pembina',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '1982-04-15',
                'address' => 'Jl. Veteran III No. 45, Ciawi, Bogor',
                'phone' => '081288990011',
                'email' => 'abdul.hamid@pmrwirasman1c.sch.id',
                'motto' => 'Mendidik relawan muda yang berkarakter, berakhlak mulia, tangguh, dan tulus mengabdi bagi kemanusiaan.',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510002',
                'name' => 'Sulthan Arkana Putra R.',
                'position' => 'Ketua Umum',
                'class_grade' => 'XI-MIPA 1',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-07-03',
                'address' => 'Komplek Griya Ciawi Asri Blok B2 No. 12, Bogor',
                'phone' => '081399887766',
                'email' => 'sulthan.arkana@pmrwirasman1c.sch.id',
                'motto' => 'Siamo Tutti Fratelli — Memimpin dengan keteladanan, melayani sesama dengan ketulusan hati.',
                'photo' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510003',
                'name' => 'Putri Raisya Anjani',
                'position' => 'Sekretaris',
                'class_grade' => 'XI-MIPA 2',
                'gender' => 'Perempuan',
                'birth_place' => 'Jakarta',
                'birth_date' => '2008-09-12',
                'address' => 'Jl. Raya Puncak Km. 72, Cisarua, Bogor',
                'phone' => '081211223344',
                'email' => 'putri.raisya@pmrwirasman1c.sch.id',
                'motto' => 'Tertib administrasi wujud integritas dan dedikasi relawan.',
                'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510004',
                'name' => 'Nisa Nurul Aulia',
                'position' => 'Bendahara',
                'class_grade' => 'XI-IPS 1',
                'gender' => 'Perempuan',
                'birth_place' => 'Bandung',
                'birth_date' => '2008-04-20',
                'address' => 'Perumahan Ciawi Permai No. 8, Ciawi',
                'phone' => '081355667788',
                'email' => 'nisa.nurul@pmrwirasman1c.sch.id',
                'motto' => 'Amanah, transparan, dan akuntabel dalam mengelola dana kemanusiaan.',
                'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510023',
                'name' => 'Imam Abdul Jabbar',
                'position' => 'Wakil Ketua Umum',
                'class_grade' => 'XI-MIPA 3',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-05-10',
                'address' => 'Jl. Rancamaya No. 15, Bogor',
                'phone' => '081233445566',
                'email' => 'imam.abdul@pmrwirasman1c.sch.id',
                'motto' => 'Berani melangkah, setia mengabdi, dan menjaga soliditas korsa demi kemanusiaan.',
                'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
        ];

        $createdMembers = [];
        foreach ($membersData as $m) {
            $createdMembers[$m['name']] = Member::create($m);
        }

        // 9. Organization Structure Settings & Members (Bagan Kepengurusan)
        OrganizationSetting::updateOrCreate(
            ['id' => 1],
            [
                'badge' => 'Bagan Kepengurusan',
                'title' => 'Struktur Organisasi 2026/2027',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara — Sinergi kepemimpinan dan dedikasi relawan siswa.',
            ]
        );

        OrganizationMember::truncate();

        $orgMembers = [
            [
                'member_id' => $createdMembers['Abdul Hamid Saputra, S.Hum., M.Pd.']->id ?? null,
                'position' => 'Pembina PMR',
                'name' => 'Abdul Hamid Saputra, S.Hum., M.Pd.',
                'subtitle' => 'Pembina Utama PMR Wira',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=256&auto=format&fit=crop&q=80',
                'level' => 1,
                'order_position' => 1,
                'icon' => 'fa-solid fa-user-tie',
                'is_active' => true,
            ],
            [
                'member_id' => $createdMembers['Sulthan Arkana Putra R.']->id ?? null,
                'position' => 'Ketua Umum 2026/2027',
                'name' => 'Sulthan Arkana Putra R.',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara',
                'photo' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=256&auto=format&fit=crop&q=80',
                'level' => 2,
                'order_position' => 1,
                'icon' => 'fa-solid fa-crown',
                'is_active' => true,
            ],
            [
                'member_id' => $createdMembers['Imam Abdul Jabbar']->id ?? null,
                'position' => 'Wakil Ketua',
                'name' => 'Imam Abdul Jabbar',
                'subtitle' => 'Wakil Ketua PMR Wira',
                'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=256&auto=format&fit=crop&q=80',
                'level' => 3,
                'order_position' => 1,
                'icon' => 'fa-solid fa-user-shield',
                'is_active' => true,
            ],
            [
                'member_id' => $createdMembers['Putri Raisya Anjani']->id ?? null,
                'position' => 'Sekretaris',
                'name' => 'Putri Raisya Anjani',
                'subtitle' => 'Administrasi & Kesekretariatan',
                'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=256&auto=format&fit=crop&q=80',
                'level' => 4,
                'order_position' => 1,
                'icon' => 'fa-solid fa-file-signature',
                'is_active' => true,
            ],
            [
                'member_id' => $createdMembers['Nisa Nurul Aulia']->id ?? null,
                'position' => 'Bendahara',
                'name' => 'Nisa Nurul Aulia',
                'subtitle' => 'Keuangan & Kas Organisasi',
                'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=256&auto=format&fit=crop&q=80',
                'level' => 4,
                'order_position' => 2,
                'icon' => 'fa-solid fa-wallet',
                'is_active' => true,
            ],
            [
                'member_id' => null,
                'position' => 'Bidang Markas',
                'name' => null,
                'subtitle' => 'Koordinator Bidang Markas & Logistik UKS',
                'photo' => null,
                'level' => 5,
                'order_position' => 1,
                'icon' => 'fa-solid fa-boxes-stacked',
                'is_active' => true,
                'staff_members' => [],
                'work_program' => "1. Pengelolaan dan inventarisasi obat-obatan serta tandu darurat UKS\n2. Pemeliharaan kebersihan, kenyamanan, dan kesiapan ruang markas PMR\n3. Pengadaan logistik medis darurat dan perawatan peralatan medis\n4. Pengaturan jadwal piket harian markas dan siaga operasional sekolah",
            ],
            [
                'member_id' => null,
                'position' => 'Bidang Pelayanan',
                'name' => null,
                'subtitle' => 'Koordinator Bidang Pelayanan Medis & Donor Darah',
                'photo' => null,
                'level' => 5,
                'order_position' => 2,
                'icon' => 'fa-solid fa-hand-holding-heart',
                'is_active' => true,
                'staff_members' => [],
                'work_program' => "1. Penyiagaan posko medis darurat pada upacara bendera dan event olahraga sekolah\n2. Pelaksanaan aksi donor darah sukarela bersama UDD PMI Kab. Bogor\n3. Pelayanan kesehatan remaja, posyandu remaja, dan pembagian Tablet Tambah Darah (TTD)\n4. Bakti sosial kemanusiaan dan kepedulian lingkungan warga sekitar sekolah",
            ],
            [
                'member_id' => null,
                'position' => 'Bidang Diklat',
                'name' => null,
                'subtitle' => 'Koordinator Bidang Pendidikan & Pelatihan (Diklat)',
                'photo' => null,
                'level' => 5,
                'order_position' => 3,
                'icon' => 'fa-solid fa-graduation-cap',
                'is_active' => true,
                'staff_members' => [],
                'work_program' => "1. Latihan rutin mingguan pertolongan pertama (PP), pembidaian, dan evakuasi\n2. Pembekalan materi 7 Prinsip Dasar Palang Merah & Hukum Humaniter Internasional\n3. Simulasi mitigasi kesiapsiagaan bencana gempa & evakuasi kebakaran sekolah\n4. Pemusatan latihan kontingen lomba Jumpa Bakti Gembira (JUMBARA)",
            ],
            [
                'member_id' => null,
                'position' => 'Bidang Humas',
                'name' => null,
                'subtitle' => 'Koordinator Bidang Hubungan Masyarakat (Humas)',
                'photo' => null,
                'level' => 5,
                'order_position' => 4,
                'icon' => 'fa-solid fa-bullhorn',
                'is_active' => true,
                'staff_members' => [],
                'work_program' => "1. Pengelolaan akun media sosial resmi dan dokumentasi seluruh agenda PMR\n2. Penerbitan buletin berkala, infografis kesehatan, dan konten edukasi medis\n3. Membangun kemitraan strategis dengan PMI Cabang, Puskesmas, dan organisasi intra sekolah\n4. Sosialisasi kepalangmerahan dan rekrutmen anggota baru PMR Wira",
            ],
            [
                'member_id' => null,
                'position' => 'Bidang Kreasi',
                'name' => null,
                'subtitle' => 'Koordinator Bidang Media Kreatif & Publikasi (Kreasi)',
                'photo' => null,
                'level' => 5,
                'order_position' => 5,
                'icon' => 'fa-solid fa-wand-magic-sparkles',
                'is_active' => true,
                'staff_members' => [],
                'work_program' => "1. Produksi media kreatif visual, video edukasi pertolongan pertama, dan podcast kesehatan\n2. Pameran karya kreasi relawan dan gelar aksi seni peringatan Hari Palang Merah Sedunia\n3. Inovasi pembuatan alat peraga simulasi medis dari bahan daur ulang ramah lingkungan\n4. Perancangan merchandise resmi, id card, dan atribut kontingen PMR Wira SMAN 1 Ciawi",
            ],
        ];

        foreach ($orgMembers as $member) {
            OrganizationMember::create($member);
        }

        // 10. Organization Guidelines (GBHO & PPUO 2026)
        OrganizationGuideline::current();
    }
}
