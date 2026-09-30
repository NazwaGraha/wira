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

        // 8. Database Members (Daftar Anggota PMR)
        \App\Models\Member::truncate();

        $membersData = [
            [
                'nis' => '242510001',
                'name' => 'Drs. H. Mulyadi, M.Pd',
                'position' => 'Pembina PMR',
                'class_grade' => 'Guru Pembina',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '1975-08-17',
                'address' => 'Jl. Veteran III No. 45, Ciawi, Bogor',
                'phone' => '081288990011',
                'email' => 'mulyadi.pembina@pmrwirasman1c.sch.id',
                'motto' => 'Mendidik relawan muda yang berkarakter, tanggap, dan berjiwa kemanusiaan.',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510002',
                'name' => 'Tom Cruz',
                'position' => 'Ketua Umum',
                'class_grade' => 'XI-MIPA 1',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-07-03',
                'address' => 'Komplek Griya Ciawi Asri Blok B2 No. 12, Bogor',
                'phone' => '081399887766',
                'email' => 'tom.cruz@pmrwirasman1c.sch.id',
                'motto' => 'Siamo Tutti Fratelli — Memimpin dengan keteladanan, melayani dengan ketulusan hati.',
                'photo' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510003',
                'name' => 'Siti Nurhaliza',
                'position' => 'Sekretaris',
                'class_grade' => 'XI-MIPA 2',
                'gender' => 'Perempuan',
                'birth_place' => 'Jakarta',
                'birth_date' => '2008-09-12',
                'address' => 'Jl. Raya Puncak Km. 72, Cisarua, Bogor',
                'phone' => '081211223344',
                'email' => 'siti.nurhaliza@pmrwirasman1c.sch.id',
                'motto' => 'Tertib administrasi adalah kunci kesuksesan setiap program kemanusiaan.',
                'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510004',
                'name' => 'Farhan Ramadhan',
                'position' => 'Bendahara',
                'class_grade' => 'XI-IPS 1',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bandung',
                'birth_date' => '2008-04-20',
                'address' => 'Perumahan Ciawi Permai No. 8, Ciawi',
                'phone' => '081355667788',
                'email' => 'farhan.ramadhan@pmrwirasman1c.sch.id',
                'motto' => 'Amanah, transparan, dan akuntabel dalam setiap rupiah dana sosial.',
                'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510005',
                'name' => 'Indra Gunawan',
                'position' => 'Ketua Bidang Markas',
                'class_grade' => 'XI-MIPA 3',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-11-05',
                'address' => 'Kp. Tipar RT 02/RW 04, Ciawi, Bogor',
                'phone' => '081277889900',
                'email' => 'indra.gunawan@pmrwirasman1c.sch.id',
                'motto' => 'Markas yang siap dan siaga adalah garda terdepan keselamatan sekolah.',
                'photo' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510006',
                'name' => 'Sarah Azhari',
                'position' => 'Ketua Bidang Pelayanan',
                'class_grade' => 'XI-MIPA 1',
                'gender' => 'Perempuan',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-03-15',
                'address' => 'Jl. Mayjen HE Sukma No. 28, Harjasari, Bogor',
                'phone' => '081344556677',
                'email' => 'sarah.azhari@pmrwirasman1c.sch.id',
                'motto' => 'Menebar senyum dan kepedulian melalui pelayanan medis penuh kasih sayang.',
                'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510007',
                'name' => 'Reza Rahadian',
                'position' => 'Ketua Bidang Diklat',
                'class_grade' => 'XI-IPS 2',
                'gender' => 'Laki-laki',
                'birth_place' => 'Sukabumi',
                'birth_date' => '2008-01-25',
                'address' => 'Jl. Caringin No. 19, Bogor',
                'phone' => '081266778899',
                'email' => 'reza.rahadian@pmrwirasman1c.sch.id',
                'motto' => 'Latihan keras melahirkan relawan tangkas dan bermental juara.',
                'photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510008',
                'name' => 'Dian Sastrowardoyo',
                'position' => 'Ketua Bidang Humas',
                'class_grade' => 'XI-MIPA 2',
                'gender' => 'Perempuan',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-06-18',
                'address' => 'Perum Banjarwangi Indah Blok C No. 5, Ciawi',
                'phone' => '081322334455',
                'email' => 'dian.sastro@pmrwirasman1c.sch.id',
                'motto' => 'Menghubungkan hati, menyuarakan pesan kemanusiaan ke penjuru dunia.',
                'photo' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510009',
                'name' => 'Nicholas Saputra',
                'position' => 'Ketua Bidang Kreasi',
                'class_grade' => 'XI-IPS 1',
                'gender' => 'Laki-laki',
                'birth_place' => 'Jakarta',
                'birth_date' => '2008-02-24',
                'address' => 'Jl. Raya Tajur No. 102, Bogor',
                'phone' => '081299001122',
                'email' => 'nicholas.saputra@pmrwirasman1c.sch.id',
                'motto' => 'Kreativitas tanpa batas untuk menginspirasi kepedulian sesama generasi muda.',
                'photo' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510010',
                'name' => 'Rian Ardiansyah',
                'position' => 'Staf Bidang Markas',
                'class_grade' => 'X-1',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2009-05-14',
                'address' => 'Jl. Pertanian No. 8, Ciawi, Bogor',
                'phone' => '081388776655',
                'email' => 'rian.ardiansyah@pmrwirasman1c.sch.id',
                'motto' => 'Siap sedia menjaga kelengkapan dan kebersihan markas relawan.',
                'photo' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510011',
                'name' => 'Siti Fatimah',
                'position' => 'Staf Bidang Markas',
                'class_grade' => 'X-3',
                'gender' => 'Perempuan',
                'birth_place' => 'Bogor',
                'birth_date' => '2009-08-09',
                'address' => 'Kp. Babakan RT 03/RW 02, Ciawi, Bogor',
                'phone' => '081299881122',
                'email' => 'siti.fatimah@pmrwirasman1c.sch.id',
                'motto' => 'Teliti dan tanggap dalam pengelolaan inventaris medis sekolah.',
                'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510012',
                'name' => 'Deni Prasetyo',
                'position' => 'Staf Bidang Markas',
                'class_grade' => 'XI-2',
                'gender' => 'Laki-laki',
                'birth_place' => 'Depok',
                'birth_date' => '2008-10-30',
                'address' => 'Jl. Gadog No. 14, Ciawi, Bogor',
                'phone' => '081311447788',
                'email' => 'deni.prasetyo@pmrwirasman1c.sch.id',
                'motto' => 'Kerja nyata untuk keselamatan dan kenyamanan bersama.',
                'photo' => 'https://images.unsplash.com/photo-1513956589380-bad6acb9b9d4?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510013',
                'name' => 'Anisa Rahma',
                'position' => 'Staf Bidang Pelayanan',
                'class_grade' => 'X-2',
                'gender' => 'Perempuan',
                'birth_place' => 'Bogor',
                'birth_date' => '2009-03-22',
                'address' => 'Kp. Cibolang RT 01/RW 05, Ciawi, Bogor',
                'phone' => '081233445566',
                'email' => 'anisa.rahma@pmrwirasman1c.sch.id',
                'motto' => 'Memberikan pertolongan pertama dengan tulus dan cepat.',
                'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510014',
                'name' => 'Budi Santoso',
                'position' => 'Staf Bidang Pelayanan',
                'class_grade' => 'XI-1',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-12-10',
                'address' => 'Jl. Sindangsari No. 50, Bogor',
                'phone' => '081377881199',
                'email' => 'budi.santoso@pmrwirasman1c.sch.id',
                'motto' => 'Siap siaga dalam setiap posko darurat dan kegiatan donor darah.',
                'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510015',
                'name' => 'Dewi Sartika',
                'position' => 'Staf Bidang Pelayanan',
                'class_grade' => 'X-5',
                'gender' => 'Perempuan',
                'birth_place' => 'Bogor',
                'birth_date' => '2009-07-19',
                'address' => 'Jl. Sukaraja No. 33, Bogor',
                'phone' => '081288334455',
                'email' => 'dewi.sartika@pmrwirasman1c.sch.id',
                'motto' => 'Menjadi pelopor gaya hidup sehat di lingkungan sekolah.',
                'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510016',
                'name' => 'Fajar Pratama',
                'position' => 'Staf Bidang Diklat',
                'class_grade' => 'X-4',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2009-01-11',
                'address' => 'Jl. Puncak No. 88, Ciawi, Bogor',
                'phone' => '081322556677',
                'email' => 'fajar.pratama@pmrwirasman1c.sch.id',
                'motto' => 'Semangat belajar materi medis demi menyelamatkan nyawa sesama.',
                'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510017',
                'name' => 'Maya Anggraini',
                'position' => 'Staf Bidang Diklat',
                'class_grade' => 'XI-3',
                'gender' => 'Perempuan',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-08-04',
                'address' => 'Jl. Pandu Raya No. 12, Bogor',
                'phone' => '081244778899',
                'email' => 'maya.anggraini@pmrwirasman1c.sch.id',
                'motto' => 'Berbagi ilmu pertolongan pertama kepada seluruh siswa sekolah.',
                'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510018',
                'name' => 'Gilang Ramadhan',
                'position' => 'Staf Bidang Humas',
                'class_grade' => 'X-1',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2009-09-09',
                'address' => 'Jl. Raya Tajur Gang Melati No. 4, Bogor',
                'phone' => '081366998811',
                'email' => 'gilang.ramadhan@pmrwirasman1c.sch.id',
                'motto' => 'Mendokumentasikan setiap aksi kemanusiaan dengan sepenuh hati.',
                'photo' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510019',
                'name' => 'Tiara Andini',
                'position' => 'Staf Bidang Humas',
                'class_grade' => 'XI-4',
                'gender' => 'Perempuan',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-05-17',
                'address' => 'Jl. Pajajaran Indah No. 7, Bogor',
                'phone' => '081255887744',
                'email' => 'tiara.andini@pmrwirasman1c.sch.id',
                'motto' => 'Menyebarkan energi positif dan kepedulian sosial melalui media digital.',
                'photo' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510020',
                'name' => 'Kevin Julio',
                'position' => 'Staf Bidang Humas',
                'class_grade' => 'X-3',
                'gender' => 'Laki-laki',
                'birth_place' => 'Jakarta',
                'birth_date' => '2009-06-28',
                'address' => 'Kp. Muara RT 02/RW 03, Ciawi, Bogor',
                'phone' => '081388112233',
                'email' => 'kevin.julio@pmrwirasman1c.sch.id',
                'motto' => 'Membangun relasi kemanusiaan yang erat antar organisasi sekolah.',
                'photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510021',
                'name' => 'Putri Marino',
                'position' => 'Staf Bidang Kreasi',
                'class_grade' => 'X-2',
                'gender' => 'Perempuan',
                'birth_place' => 'Bogor',
                'birth_date' => '2009-10-15',
                'address' => 'Jl. Ciawi Sejahtera Blok A No. 3, Bogor',
                'phone' => '081299334411',
                'email' => 'putri.marino@pmrwirasman1c.sch.id',
                'motto' => 'Kreatif berkarya demi syiar palang merah yang bersahabat.',
                'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
            [
                'nis' => '242510022',
                'name' => 'Aldi Taher',
                'position' => 'Staf Bidang Kreasi',
                'class_grade' => 'XI-5',
                'gender' => 'Laki-laki',
                'birth_place' => 'Bogor',
                'birth_date' => '2008-11-20',
                'address' => 'Jl. Sukajadi No. 18, Ciawi, Bogor',
                'phone' => '081377441122',
                'email' => 'aldi.taher@pmrwirasman1c.sch.id',
                'motto' => 'Inovasi alat medis dan poster edukasi untuk kesehatan siswa.',
                'photo' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=400&auto=format&fit=crop&q=80',
                'is_active' => true,
            ],
        ];

        $createdMembers = [];
        foreach ($membersData as $m) {
            $createdMembers[$m['name']] = \App\Models\Member::create($m);
        }

        // 9. Organization Structure Settings & Members (Bagan Kepengurusan)
        \App\Models\OrganizationSetting::updateOrCreate(
            ['id' => 1],
            [
                'badge' => 'Bagan Kepengurusan',
                'title' => 'Struktur Organisasi 2026/2027',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara — Sinergi kepemimpinan dan dedikasi relawan siswa.',
            ]
        );

        \App\Models\OrganizationMember::truncate();

        $orgMembers = [
            [
                'member_id' => $createdMembers['Drs. H. Mulyadi, M.Pd']->id ?? null,
                'position' => 'Pembina PMR',
                'name' => 'Drs. H. Mulyadi, M.Pd',
                'subtitle' => 'Pembina Ekstrakurikuler',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=256&auto=format&fit=crop&q=80',
                'level' => 1,
                'order_position' => 1,
                'icon' => 'fa-solid fa-user-tie',
                'is_active' => true,
            ],
            [
                'member_id' => $createdMembers['Tom Cruz']->id ?? null,
                'position' => 'Ketua Umum 2026/2027',
                'name' => 'Tom Cruz',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara',
                'photo' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=256&auto=format&fit=crop&q=80',
                'level' => 2,
                'order_position' => 1,
                'icon' => 'fa-solid fa-crown',
                'is_active' => true,
            ],
            [
                'member_id' => $createdMembers['Siti Nurhaliza']->id ?? null,
                'position' => 'Sekretaris',
                'name' => 'Siti Nurhaliza',
                'subtitle' => 'Administrasi & Kesekretariatan',
                'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=256&auto=format&fit=crop&q=80',
                'level' => 3,
                'order_position' => 1,
                'icon' => 'fa-solid fa-file-signature',
                'is_active' => true,
            ],
            [
                'member_id' => $createdMembers['Farhan Ramadhan']->id ?? null,
                'position' => 'Bendahara',
                'name' => 'Farhan Ramadhan',
                'subtitle' => 'Keuangan & Kas Organisasi',
                'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=256&auto=format&fit=crop&q=80',
                'level' => 3,
                'order_position' => 2,
                'icon' => 'fa-solid fa-wallet',
                'is_active' => true,
            ],
            [
                'member_id' => $createdMembers['Indra Gunawan']->id ?? null,
                'position' => 'Bidang Markas',
                'name' => 'Indra Gunawan',
                'subtitle' => 'Ketua Bidang Markas',
                'photo' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=256&auto=format&fit=crop&q=80',
                'level' => 4,
                'order_position' => 1,
                'icon' => 'fa-solid fa-boxes-stacked',
                'is_active' => true,
                'staff_members' => [
                    ['name' => 'Rian Ardiansyah', 'class_grade' => 'X-1', 'photo' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=128&auto=format&fit=crop&q=80'],
                    ['name' => 'Siti Fatimah', 'class_grade' => 'X-3', 'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=128&auto=format&fit=crop&q=80'],
                    ['name' => 'Deni Prasetyo', 'class_grade' => 'XI-2', 'photo' => 'https://images.unsplash.com/photo-1513956589380-bad6acb9b9d4?w=128&auto=format&fit=crop&q=80'],
                ],
                'work_program' => "1. Pengelolaan dan inventarisasi obat-obatan serta tandu darurat UKS\n2. Pemeliharaan kebersihan, kenyamanan, dan kesiapan ruang markas PMR\n3. Pengadaan logistik medis darurat dan perawatan peralatan medis\n4. Pengaturan jadwal piket harian markas dan siaga operasional sekolah",
            ],
            [
                'member_id' => $createdMembers['Sarah Azhari']->id ?? null,
                'position' => 'Bidang Pelayanan',
                'name' => 'Sarah Azhari',
                'subtitle' => 'Ketua Bidang Pelayanan',
                'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=256&auto=format&fit=crop&q=80',
                'level' => 4,
                'order_position' => 2,
                'icon' => 'fa-solid fa-hand-holding-heart',
                'is_active' => true,
                'staff_members' => [
                    ['name' => 'Anisa Rahma', 'class_grade' => 'X-2', 'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=128&auto=format&fit=crop&q=80'],
                    ['name' => 'Budi Santoso', 'class_grade' => 'XI-1', 'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=128&auto=format&fit=crop&q=80'],
                    ['name' => 'Dewi Sartika', 'class_grade' => 'X-5', 'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=128&auto=format&fit=crop&q=80'],
                ],
                'work_program' => "1. Penyiagaan posko medis darurat pada upacara bendera dan event olahraga sekolah\n2. Pelaksanaan aksi donor darah sukarela bersama UDD PMI Kab. Bogor\n3. Pelayanan kesehatan remaja, posyandu remaja, dan pembagian Tablet Tambah Darah (TTD)\n4. Bakti sosial kemanusiaan dan kepedulian lingkungan warga sekitar sekolah",
            ],
            [
                'member_id' => $createdMembers['Reza Rahadian']->id ?? null,
                'position' => 'Bidang Diklat',
                'name' => 'Reza Rahadian',
                'subtitle' => 'Ketua Bidang Diklat',
                'photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=256&auto=format&fit=crop&q=80',
                'level' => 4,
                'order_position' => 3,
                'icon' => 'fa-solid fa-graduation-cap',
                'is_active' => true,
                'staff_members' => [
                    ['name' => 'Fajar Pratama', 'class_grade' => 'X-4', 'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=128&auto=format&fit=crop&q=80'],
                    ['name' => 'Maya Anggraini', 'class_grade' => 'XI-3', 'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=128&auto=format&fit=crop&q=80'],
                ],
                'work_program' => "1. Latihan rutin mingguan pertolongan pertama (PP), pembidaian, dan evakuasi\n2. Pembekalan materi 7 Prinsip Dasar Palang Merah & Hukum Humaniter Internasional\n3. Simulasi mitigasi kesiapsiagaan bencana gempa & evakuasi kebakaran sekolah\n4. Pemusatan latihan kontingen lomba Jumpa Bakti Gembira (JUMBARA)",
            ],
            [
                'member_id' => $createdMembers['Dian Sastrowardoyo']->id ?? null,
                'position' => 'Bidang Humas',
                'name' => 'Dian Sastrowardoyo',
                'subtitle' => 'Ketua Bidang Humas',
                'photo' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=256&auto=format&fit=crop&q=80',
                'level' => 4,
                'order_position' => 4,
                'icon' => 'fa-solid fa-bullhorn',
                'is_active' => true,
                'staff_members' => [
                    ['name' => 'Gilang Ramadhan', 'class_grade' => 'X-1', 'photo' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=128&auto=format&fit=crop&q=80'],
                    ['name' => 'Tiara Andini', 'class_grade' => 'XI-4', 'photo' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=128&auto=format&fit=crop&q=80'],
                    ['name' => 'Kevin Julio', 'class_grade' => 'X-3', 'photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=128&auto=format&fit=crop&q=80'],
                ],
                'work_program' => "1. Pengelolaan akun media sosial resmi dan dokumentasi seluruh agenda PMR\n2. Penerbitan buletin berkala, infografis kesehatan, dan konten edukasi medis\n3. Membangun kemitraan strategis dengan PMI Cabang, Puskesmas, dan organisasi intra sekolah\n4. Sosialisasi kepalangmerahan dan rekrutmen anggota baru PMR Wira",
            ],
            [
                'member_id' => $createdMembers['Nicholas Saputra']->id ?? null,
                'position' => 'Bidang Kreasi',
                'name' => 'Nicholas Saputra',
                'subtitle' => 'Ketua Bidang Kreasi',
                'photo' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=256&auto=format&fit=crop&q=80',
                'level' => 4,
                'order_position' => 5,
                'icon' => 'fa-solid fa-wand-magic-sparkles',
                'is_active' => true,
                'staff_members' => [
                    ['name' => 'Putri Marino', 'class_grade' => 'X-2', 'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=128&auto=format&fit=crop&q=80'],
                    ['name' => 'Aldi Taher', 'class_grade' => 'XI-5', 'photo' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=128&auto=format&fit=crop&q=80'],
                ],
                'work_program' => "1. Produksi media kreatif visual, video edukasi pertolongan pertama, dan podcast kesehatan\n2. Pameran karya kreasi relawan dan gelar aksi seni peringatan Hari Palang Merah Sedunia\n3. Inovasi pembuatan alat peraga simulasi medis dari bahan daur ulang ramah lingkungan\n4. Perancangan merchandise resmi, id card, dan atribut kontingen PMR Wira SMAN 1 Ciawi",
            ],
        ];

        foreach ($orgMembers as $member) {
            \App\Models\OrganizationMember::create($member);
        }
    }
}
