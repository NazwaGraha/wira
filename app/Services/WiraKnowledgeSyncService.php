<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Article;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\Member;
use App\Models\OrganizationMember;
use App\Models\PopupBanner;
use App\Models\UksHotline;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WiraKnowledgeSyncService
{
    /**
     * Jalankan sinkronisasi seluruh konten website ke storage knowledge base Wira Agent.
     *
     * @return array<string, mixed>
     */
    public function syncAll(): array
    {
        $topics = [];

        // 1. Profil Organisasi & Identitas Markas
        $topics[] = $this->buildProfileTopic();

        // 2. Visi, Misi & Semboyan Organisasi
        $topics[] = $this->buildVisiMisiTopic();

        // 3. Hotline Siaga UKS
        $topics[] = $this->buildHotlineUksTopic();

        // 4. Struktur Organisasi & Bagan Kepengurusan Lengkap
        $topics[] = $this->buildOrganizationStructureTopic();

        // 4b. Program Kerja 5 Bidang Utama (Dinamis dari Database)
        $topics = array_merge($topics, $this->buildBidangWorkProgramsTopics());

        // 5. Biodata Pimpinan & Dewan Pembina
        $topics = array_merge($topics, $this->buildLeadershipBiodataTopics());

        // 6. Biodata Semua Anggota Terdaftar
        $topics = array_merge($topics, $this->buildAllMembersTopics());

        // 7. Pedoman Organisasi: GBHO 2026 & PPUO 2026
        $topics[] = $this->buildGuidelinesTopic();

        // 8. Seluruh Artikel & Berita Edukasi yang Diterbitkan
        $articleTopics = $this->buildArticlesTopics();
        $topics = array_merge($topics, $articleTopics);

        // 9. Agenda & Seluruh Kegiatan Organisasi
        $topics[] = $this->buildActivitiesTopic();

        // 10. Etalase Prestasi Juara (Hall of Fame)
        $topics[] = $this->buildAchievementsTopic();

        // 11. Info Lomba PMR (Sua Bhakti Berkarya & Cabang Lomba)
        $topics[] = $this->buildCompetitionTopic();

        // 12. Layanan Donor Darah Sukarela & Stok Darah
        $topics[] = $this->buildBloodDonationTopic();

        // 13. Galeri Dokumentasi Kegiatan
        $topics[] = $this->buildGalleryTopic();

        // 14. Pengumuman Popup Banner Aktif
        $topics[] = $this->buildPopupBannerTopic();

        // 15. Standar Kurikulum Kepalangmerahan PMI & KSR
        $topics = array_merge($topics, $this->buildCurriculumTopics());

        // Bersihkan duplikasi ID topik jika ada
        $uniqueTopics = [];
        $seenIds = [];
        foreach ($topics as $top) {
            $id = $top['id'] ?? Str::slug($top['title'] ?? uniqid());
            if (! isset($seenIds[$id])) {
                $seenIds[$id] = true;
                $uniqueTopics[] = $top;
            }
        }

        // Tulis ke storage/app/knowledge/knowledge.json
        $knowledgeData = [
            'version' => '2.0.0',
            'organization' => 'PMR Wira SMAN 1 Ciawi',
            'cabinet' => 'Ragana Dwi Pantara 2026/2027',
            'updated_at' => now()->toIso8601String(),
            'total_topics' => count($uniqueTopics),
            'topics' => $uniqueTopics,
        ];

        $storageDir = storage_path('app/knowledge');
        if (! File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        File::put(
            $storageDir.'/knowledge.json',
            json_encode($knowledgeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        // Bersihkan cache memori KnowledgeSearchService
        try {
            app(KnowledgeSearchService::class)->clearCache();
        } catch (\Throwable $e) {
        }

        return [
            'success' => true,
            'total_topics' => count($uniqueTopics),
            'articles_count' => count($articleTopics),
            'synced_at' => now()->translatedFormat('d F Y H:i:s'),
            'file_path' => $storageDir.'/knowledge.json',
        ];
    }

    /**
     * Topik 1: Profil Organisasi
     */
    protected function buildProfileTopic(): array
    {
        return [
            'id' => 'profil_organisasi',
            'category' => 'organisasi',
            'title' => 'Profil & Identitas PMR Wira SMAN 1 Ciawi',
            'keywords' => ['profil', 'alamat', 'lokasi', 'kontak', 'email', 'markas', 'sekretariat', 'uks', 'sekolah', 'pandansari', 'veteran', 'terakreditasi a', 'sman 1 ciawi'],
            'content' => 'PMR Wira Unit SMA Negeri 1 Ciawi adalah organisasi relawan sekolah terakreditasi A berkedudukan di Ruang UKS/PMR SMAN 1 Ciawi, Jl. Veteran No. 46, Pandansari, Ciawi, Kabupaten Bogor (Kode Pos 16720). Email resmi: pmrwirasman1c@gmail.com. Hotline Siaga Medis UKS: 0857-7024-6918. Masa bakti kepengurusan aktif adalah Ragana Dwi Pantara 2026/2027.',
        ];
    }

    /**
     * Topik 2: Visi, Misi & Semboyan
     */
    protected function buildVisiMisiTopic(): array
    {
        return [
            'id' => 'semboyan_visi_misi',
            'category' => 'organisasi',
            'title' => 'Visi, Misi & Semboyan Palang Merah (Siamo Tutti Fratelli)',
            'keywords' => ['visi', 'misi', 'semboyan', 'siamo tutti fratelli', 'prinsip organisasi', 'tujuan pmr'],
            'content' => 'Semboyan Palang Merah: "Siamo Tutti Fratelli" (Kita Semua Bersaudara / We are all brothers). Visi PMR Wira SMAN 1 Ciawi: Menjadi unit Palang Merah Remaja Wira yang unggul, berkarakter, responsif, berprestasi, dan terdepan dalam aksi kemanusiaan berlandaskan 7 Prinsip Dasar PMI dan Tri Bakti PMR. 5 Misi Utama: 1. Menanamkan nilai kepalangmerahan & kepemimpinan siswa; 2. Menyelenggarakan pelatihan P3K dan mitigasi bencana terstandarisasi; 3. Mengelola Pos Kesehatan UKS yang sigap melayani warga sekolah; 4. Menggalang kepedulian sosial via aksi donor darah sukarela bersama UTD PMI Kab. Bogor; 5. Menjalin persahabatan dan mencetak prestasi lomba kemanusiaan di tingkat wilayah dan nasional.',
        ];
    }

    /**
     * Topik 3: Hotline Siaga UKS
     */
    protected function buildHotlineUksTopic(): array
    {
        $phone = '0857-7024-6918';
        $hours = 'Senin s.d. Jumat (07.00 - 16.00 WIB)';
        $officer = 'Petugas Medis Siaga UKS';

        try {
            if (Schema::hasTable('uks_hotlines')) {
                $hotline = UksHotline::current();
                $phone = $hotline->clean_phone ?: $hotline->phone_number;
                $hours = $hotline->operational_hours ?: $hours;
                $officer = $hotline->officer_name ?: $officer;
            }
        } catch (\Throwable $e) {
        }

        return [
            'id' => 'hotline_uks',
            'category' => 'layanan_uks',
            'title' => 'Layanan Hotline Siaga Medis UKS & Panggilan Darurat',
            'keywords' => ['hotline', 'uks', 'telepon', 'whatsapp', 'wa', 'nomor darurat', 'posko medis', 'piket uks', 'piket medis'],
            'content' => "Hotline Siaga UKS SMAN 1 Ciawi: {$phone} (WhatsApp & Telepon Cepat Langsung). Jam Layanan: {$hours}. Petugas Siaga: {$officer}. Fungsi: Penanganan pertolongan pertama (P3K) siswa sakit atau cedera saat upacara bendera, jam KBM, penugasan medis event sekolah, serta rujukan cepat ke Puskesmas Ciawi / Rumah Sakit terdekat.",
        ];
    }

    /**
     * Topik 4: Struktur Organisasi Lengkap
     */
    protected function buildOrganizationStructureTopic(): array
    {
        $bidangDefs = [
            ['name' => 'Bidang Markas', 'keywords' => ['markas', 'logistik'], 'order' => 1, 'desc' => 'Pengelolaan inventaris obat UKS, perawatan tandu darurat & spalk bidai, piket markas.'],
            ['name' => 'Bidang Pelayanan', 'keywords' => ['pelayanan', 'kesehatan'], 'order' => 2, 'desc' => 'Siaga posko medis upacara bendera, donor darah sukarela bersama UTD PMI Kab. Bogor, posyandu remaja & pembagian TTD.'],
            ['name' => 'Bidang Diklat', 'keywords' => ['diklat', 'pelatihan', 'pendidikan'], 'order' => 3, 'desc' => 'Silabus kurikulum P3K, Diklatsar ruang dan lapangan, latihan mingguan, pembinaan lomba.'],
            ['name' => 'Bidang Humas', 'keywords' => ['humas', 'hubungan masyarakat'], 'order' => 4, 'desc' => 'Publikasi media, kemitraan PMI Kab. Bogor, rekrutmen anggota.'],
            ['name' => 'Bidang Kreasi', 'keywords' => ['kreasi', 'kreatif'], 'order' => 5, 'desc' => 'Desain poster edukasi, video materi P3K, merchandise dan ID card relawan.'],
        ];

        $orgMembers = collect();
        if (Schema::hasTable('organization_members')) {
            try {
                $orgMembers = OrganizationMember::with('member')->where('is_active', true)->get();
            } catch (\Throwable $e) {
            }
        }

        $bidangLines = [];
        foreach ($bidangDefs as $b) {
            $matchedOrg = null;
            foreach ($orgMembers->where('level', 5) as $om) {
                $posText = strtolower(($om->position ?? '').' '.($om->subtitle ?? '').' '.($om->name ?? ''));
                foreach ($b['keywords'] as $kw) {
                    if (str_contains($posText, $kw)) {
                        $matchedOrg = $om;
                        break 2;
                    }
                }
            }

            $ketuaName = null;
            $ketuaDetail = '';
            $stafNames = [];

            if ($matchedOrg) {
                if ($matchedOrg->member) {
                    $ketuaName = $matchedOrg->member->name;
                    $kKelas = $matchedOrg->member->class_grade ? 'Kelas '.$matchedOrg->member->class_grade : 'Kelas XI';
                    $kNri = $matchedOrg->member->nri ? 'NRI: '.$matchedOrg->member->nri : '-';
                    $ketuaDetail = " ({$kKelas} • {$kNri})";
                } elseif (! empty($matchedOrg->name)) {
                    $ketuaName = $matchedOrg->name;
                }

                if (! empty($matchedOrg->staff_members) && is_array($matchedOrg->staff_members)) {
                    foreach ($matchedOrg->staff_members as $st) {
                        if (is_array($st) && ! empty($st['name'])) {
                            $stafNames[] = $st['name'];
                        }
                    }
                }
            }

            if (! $ketuaName && Schema::hasTable('members')) {
                try {
                    foreach ($b['keywords'] as $kw) {
                        $m = Member::where(function ($q) use ($kw) {
                            $q->where('position', 'like', '%ketua%'.$kw.'%')
                                ->orWhere('position', 'like', '%kabid%'.$kw.'%');
                        })->where('is_active', true)->first();

                        if ($m) {
                            $ketuaName = $m->name;
                            $kKelas = $m->class_grade ? 'Kelas '.$m->class_grade : 'Kelas XI';
                            $kNri = $m->nri ? 'NRI: '.$m->nri : '-';
                            $ketuaDetail = " ({$kKelas} • {$kNri})";
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                }
            }

            $line = "  {$b['order']}. {$b['name']}: ";
            if ($ketuaName) {
                $line .= "Ketua {$ketuaName}{$ketuaDetail}";
                if (! empty($stafNames)) {
                    $line .= ', Staf: '.implode(', ', $stafNames);
                }
                $line .= ". Tugas: {$b['desc']}";
            } else {
                $line .= "(Ketua belum ditetapkan di sistem). Tugas: {$b['desc']}";
            }
            $bidangLines[] = $line;
        }

        $content = "Struktur Organisasi PMR Wira SMAN 1 Ciawi Masa Bakti Ragana Dwi Pantara 2026/2027 terdiri dari:\n"
            ."• Level 1 (Dewan Pembina): Pembina Utama Abdul Hamid Saputra, S.Hum., M.Pd. (NIP: 19820415 200801 1 008) & Drs. H. Mulyadi, M.Pd.\n"
            ."• Level 2 (Ketua Umum): Sulthan Arkana Putra R. (Kelas XI • NRI: 24.49.004 PRT)\n"
            ."• Level 3 (Wakil Ketua Umum): Imam Abdul Jabbar (Kelas XI • NRI: 24.49.012 PRT)\n"
            ."• Level 4 (BPH): Sekretaris Umum Putri Raisya Anjani (Kelas XI • NRI: 24.49.018 PRT) & Bendahara Umum Nisa Nurul Aulia (Kelas XI • NRI: 24.49.023 PRT)\n"
            ."• Level 5 (5 Bidang Utama & Staf):\n"
            .implode("\n", $bidangLines);

        return [
            'id' => 'struktur_organisasi_lengkap',
            'category' => 'struktur',
            'title' => 'Bagan Struktur Organisasi Lengkap (Dari Pembina Sampai Seluruh Staf)',
            'keywords' => ['struktur', 'bagan', 'organisasi', 'susunan pengurus', 'daftar pengurus', 'hierarki', 'semua anggota', 'pembina sampai bawah', 'kabinet', 'ragana dwi pantara', '5 bidang'],
            'content' => $content,
        ];
    }

    /**
     * Topik 5: Biodata Pimpinan Utama & Pembina
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildLeadershipBiodataTopics(): array
    {
        return [
            [
                'id' => 'biodata_pembina',
                'category' => 'biodata',
                'title' => 'Biodata Pembina PMR Wira: Abdul Hamid Saputra, S.Hum., M.Pd.',
                'keywords' => ['pembina', 'abdul hamid', 'saputra', 'mulyadi', 'guru pembimbing', 'biodata pembina', 'nip pembina'],
                'content' => "KARTU TANDA ANGGOTA & BIODATA PEMBINA PMR WIRA\n• Nama Lengkap: Abdul Hamid Saputra, S.Hum., M.Pd.\n• Jabatan: Pembina Utama PMR Wira SMAN 1 Ciawi\n• Status / NIP: 19820415 200801 1 008\n• Masa Bakti: 2026/2027 (Ragana Dwi Pantara)\n• Tugas Pokok: Membimbing, mengarahkan, dan mengawasi program kerja kepalangmerahan, pembinaan karakter relawan, serta sinergi sekolah dan PMI Cabang.\n• Motto: 'Mendidik relawan muda yang berkarakter, berakhlak mulia, tangguh, dan tulus mengabdi bagi kemanusiaan.'",
            ],
            [
                'id' => 'biodata_ketua_umum',
                'category' => 'biodata',
                'title' => 'Biodata Ketua Umum: Sulthan Arkana Putra R.',
                'keywords' => ['ketua umum', 'ketua pmr', 'sulthan', 'arkana', 'putra', 'pemimpin', 'biodata ketua', 'nri ketua'],
                'content' => "KARTU TANDA ANGGOTA & BIODATA KETUA UMUM PMR WIRA\n• Nama Lengkap: Sulthan Arkana Putra R.\n• Jabatan: Ketua Umum PMR Wira SMAN 1 Ciawi\n• Kelas / Unit: Kelas XI • PMR Wira SMAN 1 Ciawi\n• NRI: 24.49.004 PRT\n• Masa Bakti: 2026/2027 (Ragana Dwi Pantara)\n• Tugas: Memimpin organisasi, penanggung jawab umum program kerja, koordinasi pihak sekolah & PMI, serta mengarahkan 5 bidang operasional.\n• Motto: 'Siamo Tutti Fratelli — Memimpin dengan keteladanan, melayani sesama dengan ketulusan hati.'",
            ],
            [
                'id' => 'biodata_wakil_ketua',
                'category' => 'biodata',
                'title' => 'Biodata Wakil Ketua Umum: Imam Abdul Jabbar',
                'keywords' => ['wakil ketua', 'imam', 'abdul jabbar', 'wakil', 'biodata wakil', 'nri wakil'],
                'content' => "KARTU TANDA ANGGOTA & BIODATA WAKIL KETUA UMUM PMR WIRA\n• Nama Lengkap: Imam Abdul Jabbar\n• Jabatan: Wakil Ketua Umum PMR Wira\n• Kelas / Unit: Kelas XI • PMR Wira SMAN 1 Ciawi\n• NRI: 24.49.012 PRT\n• Masa Bakti: 2026/2027 (Ragana Dwi Pantara)\n• Tugas: Mendampingi Ketua Umum, mengoordinasikan internal 5 bidang, pengawasan kedisiplinan, dan evaluasi bulanan.\n• Motto: 'Berani melangkah, setia mengabdi, dan menjaga soliditas korsa demi kemanusiaan.'",
            ],
            [
                'id' => 'biodata_sekretaris',
                'category' => 'biodata',
                'title' => 'Biodata Sekretaris Umum: Putri Raisya Anjani',
                'keywords' => ['sekretaris', 'putri', 'raisya', 'anjani', 'bph', 'administrasi', 'biodata sekretaris', 'nri sekretaris'],
                'content' => "KARTU TANDA ANGGOTA & BIODATA SEKRETARIS UMUM PMR WIRA\n• Nama Lengkap: Putri Raisya Anjani\n• Jabatan: Sekretaris Umum (BPH)\n• Kelas / Unit: Kelas XI • PMR Wira SMAN 1 Ciawi\n• NRI: 24.49.018 PRT\n• Masa Bakti: 2026/2027 (Ragana Dwi Pantara)\n• Tugas: Pengelolaan administrasi, persuratan mandat lomba & dinas, notulensi rapat, dan buku induk relawan.\n• Motto: 'Tertib administrasi wujud integritas dan dedikasi relawan.'",
            ],
            [
                'id' => 'biodata_bendahara',
                'category' => 'biodata',
                'title' => 'Biodata Bendahara Umum: Nisa Nurul Aulia',
                'keywords' => ['bendahara', 'nisa', 'nurul', 'aulia', 'keuangan', 'kas', 'bph', 'biodata bendahara', 'nri bendahara'],
                'content' => "KARTU TANDA ANGGOTA & BIODATA BENDAHARA UMUM PMR WIRA\n• Nama Lengkap: Nisa Nurul Aulia\n• Jabatan: Bendahara Umum (BPH)\n• Kelas / Unit: Kelas XI • PMR Wira SMAN 1 Ciawi\n• NRI: 24.49.023 PRT\n• Masa Bakti: 2026/2027 (Ragana Dwi Pantara)\n• Tugas: Perbendaharaan kas organisasi, pembukuan keuangan, anggaran logistik medis UKS, dan pelaporan transparansi dana.\n• Motto: 'Amanah, transparan, dan akuntabel dalam mengelola dana kemanusiaan.'",
            ],
        ];
    }

    /**
     * Topik 6: Biodata Semua Anggota dari Database (Model Member)
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildAllMembersTopics(): array
    {
        $memberTopics = [];

        try {
            if (Schema::hasTable('members')) {
                $members = Member::all();
                foreach ($members as $m) {
                    $name = $m->name;
                    $position = $m->position ?: 'Anggota Relawan';
                    $nri = $m->nri ?: '-';
                    $class = $m->class_grade ? 'Kelas '.$m->class_grade : 'Kelas X/XI';
                    $motto = $m->motto ?: 'Bakti Kemanusiaan untuk Sesama';
                    $phone = $m->phone ?: '-';
                    $email = $m->email ?: '-';
                    $address = $m->address ?: '-';
                    $gender = $m->gender ?: '-';
                    $ttl = ($m->birth_place && $m->birth_date) ? "{$m->birth_place}, ".Carbon::parse($m->birth_date)->translatedFormat('d F Y') : '-';
                    $status = $m->effective_status ?: 'Aktif';
                    $photoUrl = $m->photo_url;

                    $slug = Str::slug($name);
                    $nameLower = strtolower($name);

                    $keywords = [
                        $nameLower,
                        'biodata '.$nameLower,
                        'siapakah '.$nameLower,
                        'siapa '.$nameLower,
                        $nameLower.' jabatannya apa',
                        'jabatan '.$nameLower,
                        'kontak '.$nameLower,
                        'no hp '.$nameLower,
                        'alamat '.$nameLower,
                        'kelas '.$nameLower,
                        strtolower($position),
                        'nri '.$nri,
                        'anggota pmr',
                        $nri,
                    ];

                    // Tambahkan potongan nama untuk pencarian kata parsial
                    $nameParts = preg_split('/\s+/', $nameLower);
                    foreach ($nameParts as $part) {
                        if (strlen($part) >= 3) {
                            $keywords[] = $part;
                        }
                    }

                    $memberTopics[] = [
                        'id' => 'member_'.$slug,
                        'category' => 'biodata_anggota',
                        'title' => "Biodata Anggota PMR: {$name} ({$position})",
                        'keywords' => array_values(array_unique(array_filter($keywords))),
                        'content' => "KARTU TANDA ANGGOTA & BIODATA RELAWAN PMR WIRA\n"
                            ."• Nama Lengkap: {$name}\n"
                            ."• Jabatan Resmi: {$position}\n"
                            ."• Tingkat / Kelas: {$class} • PMR Wira SMAN 1 Ciawi\n"
                            ."• NRI (Nomor Registrasi Induk): {$nri} (Status: {$status})\n"
                            ."• Tempat / Tanggal Lahir: {$ttl}\n"
                            ."• Jenis Kelamin: {$gender}\n"
                            ."• Alamat / Domisili: {$address}\n"
                            ."• Kontak Telepon / WA: {$phone}\n"
                            ."• Email: {$email}\n"
                            ."• Foto Profil: {$photoUrl}\n"
                            ."• Motto Relawan: '{$motto}'",
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        return $memberTopics;
    }

    /**
     * Topik 7: Pedoman Organisasi (GBHO & PPUO 2026)
     */
    protected function buildGuidelinesTopic(): array
    {
        return [
            'id' => 'landasan_gbho_ppuo',
            'category' => 'pedoman_organisasi',
            'title' => 'Landasan Pedoman Organisasi (GBHO & PPUO 2026)',
            'keywords' => ['gbho', 'ppuo', 'pedoman', 'aturan', 'syal kuning', 'anggaran rumah tangga', 'konstitusi', 'tata tertib', 'kebijakan', 'strategi'],
            'content' => "Konstitusi Resmi PMR Wira SMAN 1 Ciawi Periode 2026:\n"
                ."1. Garis Besar Haluan Organisasi (GBHO) 2026: Memuat 6 Strategi Pokok (Kedisiplinan & Korsa, Pelatihan Medis Terstandar, Pembinaan Fisik & Mental, Soliditas Anggota, Kepemimpinan Mandiri, Sinergi Pembina/Sekolah/PMI/Alumni).\n"
                ."2. Pedoman Pelaksanaan Umum Organisasi (PPUO) 2026: Terdiri atas 11 Bab Regulasi (Keanggotaan, Seragam & Atribut, Markas UKS, Kode Etik, Rapat RAT/Raker, Tata Tertib & Sanksi, Diklatsar, Penugasan Medis, Kas Keuangan, Aturan Peralihan).\n"
                .'3. Spesifikasi Syal Resmi PMR Wira: Syal Kuning Segitiga PMI berukuran 120 cm x 90 cm x 90 cm dengan logo PMI di sudut lancip belakang, dilipat rapi memakai ring cincin syal.',
        ];
    }

    /**
     * Topik 8: Seluruh Artikel Publik dari Database
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildArticlesTopics(): array
    {
        $articleTopics = [];

        try {
            if (Schema::hasTable('articles')) {
                $articles = Article::with('category')->where('status', 'published')->get();

                foreach ($articles as $art) {
                    $title = $art->title;
                    $categoryName = $art->category ? $art->category->name : 'Informasi & Edukasi';
                    $rawBody = $art->body ?? $art->content ?? '';
                    $excerpt = $art->excerpt ?: Str::limit(strip_tags($rawBody), 200);
                    $cleanBody = Str::limit(strip_tags($rawBody), 800);

                    $keywords = [
                        'artikel',
                        strtolower($title),
                        strtolower($categoryName),
                        'berita pmr',
                    ];

                    $articleTopics[] = [
                        'id' => 'artikel_'.$art->id,
                        'category' => 'artikel',
                        'title' => "Artikel: {$title}",
                        'keywords' => $keywords,
                        'content' => "ARTIKEL RESMI PMR WIRA SMAN 1 CIAWI\n• Judul: {$title}\n• Kategori: {$categoryName}\n• Ringkasan: {$excerpt}\n• Isi Ulasan: {$cleanBody}\n• Baca selengkapnya di menu Artikel website https://pmrwirasmanic.or.id",
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        return $articleTopics;
    }

    /**
     * Topik 9: Seluruh Agenda & Kegiatan
     */
    protected function buildActivitiesTopic(): array
    {
        $content = "AGENDA & DOKUMENTASI KEGIATAN PMR WIRA SMAN 1 CIAWI:\n";
        try {
            if (Schema::hasTable('activities')) {
                $activities = Activity::orderByDesc('event_date')->take(10)->get();
                if ($activities->isNotEmpty()) {
                    foreach ($activities as $act) {
                        $dateStr = $act->event_date ? $act->event_date->format('d M Y') : 'Jadwal Rutin';
                        $loc = $act->location ?: 'Kampus SMAN 1 Ciawi';
                        $desc = Str::limit(strip_tags($act->description ?: ''), 120);
                        $content .= "• {$act->title} ({$dateStr} - Lokasi: {$loc}): {$desc}\n";
                    }
                } else {
                    $content .= "• Pelatihan Pertolongan Pertama (PP) Wira, Aksi Donor Darah Sukarela, Simulasi Sekolah Aman Bencana, Jumpa Bakti Gembira (Jumbara), dan Bakti Sosial Masyarakat.\n";
                }
            }
        } catch (\Throwable $e) {
            $content .= "• Kegiatan utama: Diklatsar ruang dan lapangan, latihan rutin mingguan, aksi donor darah sukarela bersama UDD PMI Kab. Bogor, penugasan medis upacara sekolah, dan simulasi bencana.\n";
        }

        return [
            'id' => 'kegiatan_dan_agenda',
            'category' => 'kegiatan',
            'title' => 'Agenda & Program Kegiatan Relawan PMR Wira',
            'keywords' => ['kegiatan', 'agenda', 'program kerja', 'acara', 'pelatihan', 'latgab', 'diklatsar', 'jumbara', 'baksos', 'simulasi'],
            'content' => trim($content),
        ];
    }

    /**
     * Topik 10: Prestasi & Penghargaan (Hall of Fame)
     */
    protected function buildAchievementsTopic(): array
    {
        $content = "ETALASE PRESTASI JUARA (HALL OF FAME) PMR WIRA SMAN 1 CIAWI:\n"
            ."• Statistik: 48+ Piala & Trofi, 14 Gelar Juara Umum, 6 Prestasi Nasional, 100% Jiwa Relawan.\n";

        try {
            if (Schema::hasTable('achievements')) {
                $achievements = Achievement::orderByDesc('id')->get();
                if ($achievements->isNotEmpty()) {
                    $content .= "• Daftar Kejuaraan Terbaru:\n";
                    foreach ($achievements as $ach) {
                        $content .= "  - {$ach->title} ({$ach->event_name} • {$ach->rank} {$ach->level})\n";
                    }
                } else {
                    $content .= "  - Juara Umum I Lomba Ketangkasan PP Se-Jawa Barat (28 Feb 2026)\n"
                        ."  - Juara 1 Pertolongan Pertama Medis Darurat Nasional\n"
                        ."  - Juara 1 Pasang Bongkar Tandu Darurat & Evakuasi Cepat\n"
                        ."  - Juara 1 Cepat Tepat Kepalangmerahan & Hukum Humaniter\n";
                }
            }
        } catch (\Throwable $e) {
            $content .= "• Juara Umum I Tingkat Provinsi Jawa Barat, Juara 1 Pertolongan Pertama, Juara 1 Tandu Darurat, Juara 1 Cepat Tepat.\n";
        }

        return [
            'id' => 'prestasi_juara',
            'category' => 'prestasi',
            'title' => 'Rekam Jejak Prestasi Juara & Hall of Fame PMR Wira',
            'keywords' => ['prestasi', 'juara', 'piala', 'trofi', 'penghargaan', 'lomba jabar', 'juara umum', 'hall of fame'],
            'content' => trim($content),
        ];
    }

    /**
     * Topik 11: Lomba PMR
     */
    protected function buildCompetitionTopic(): array
    {
        $title = 'Lomba Ketangkasan PMR Wira Tingkat Jawa Barat (Sua Bhakti Berkarya)';
        $feeInfo = 'Rp 150.000 / Regu';
        $location = 'Kampus SMA Negeri 1 Ciawi, Bogor';
        $categoriesList = 'Pertolongan Pertama (PP), Pasang Tandu Darurat Cepat, Traveling Kepalangmerahan, Video & Poster Kemanusiaan.';

        try {
            if (Schema::hasTable('competition_events')) {
                $event = CompetitionEvent::where('is_active', true)->first() ?: CompetitionEvent::first();
                if ($event) {
                    $title = $event->title;
                    $location = $event->location ?: $location;
                    if ($event->registration_fee > 0) {
                        $feeInfo = 'Rp '.number_format($event->registration_fee, 0, ',', '.').' / Regu';
                    }

                    if (Schema::hasTable('competition_categories')) {
                        $cats = CompetitionCategory::where('competition_event_id', $event->id)->where('is_active', true)->get();
                        if ($cats->isNotEmpty()) {
                            $categoriesList = $cats->pluck('name')->implode(', ');
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        $content = "INFORMASI LOMBA PMR: {$title}\n"
            ."• Lokasi: {$location}\n"
            ."• Biaya Registrasi: {$feeInfo}\n"
            ."• Cabang Lomba: {$categoriesList}\n"
            ."• Syarat Peserta:\n"
            ."  1. Anggota aktif PMR Madya (SMP/MTs) atau PMR Wira (SMA/SMK/MA).\n"
            ."  2. Membawa Surat Tugas/Mandat resmi dari Kepala Sekolah / Pembina.\n"
            ."  3. Melampirkan Kartu Pelajar atau Surat Keterangan Siswa.\n"
            ."  4. Pas foto 3x4 berseragam PMR lengkap syal.\n"
            ."  5. Mendaftar online di menu Lomba PMR website https://pmrwirasmanic.or.id/lomba.\n"
            .'  6. Melunasi administrasi dan membawa perlengkapan teknis mandiri.';

        return [
            'id' => 'lomba_pmr_info',
            'category' => 'lomba',
            'title' => 'Informasi Lomba PMR & Syarat Pendaftaran Kontingen',
            'keywords' => ['lomba', 'sua bhakti', 'syarat lomba', 'daftar lomba', 'kategori lomba', 'biaya lomba', 'juklak', 'juknis', 'kompetisi'],
            'content' => $content,
        ];
    }

    /**
     * Topik 12: Layanan Donor Darah
     */
    protected function buildBloodDonationTopic(): array
    {
        $content = "AKSI DONOR DARAH SUKARELA & STOK DARAH PMR WIRA SMAN 1 CIAWI:\n"
            ."• Bekerja sama rutin dengan UTD PMI Kabupaten Bogor.\n"
            ."• Syarat Menjadi Pendonor:\n"
            ."  - Usia 17 s.d. 60 tahun.\n"
            ."  - Berat badan minimal 45 kg.\n"
            ."  - Tekanan darah normal (Sistole 100-160, Diastole 70-100 mmHg).\n"
            ."  - Kadar Hemoglobin (Hb) minimal 12,5 g/dL.\n"
            ."  - Jarak donor minimal 60 - 90 hari (2 - 3 bulan).\n"
            ."  - Tidak minum obat keras/antibiotik 3 hari terakhir.\n"
            ."  - Wanita tidak sedang hamil, menyusui, atau haid hari 1-2.\n"
            ."• Golongan Darah yang Dilayani: A, B, AB, dan O (Rhesus Positif & Negatif).\n"
            .'• Informasi jadwal donor darah kampus dan ketersediaan stok dapat dipantau di menu Donor Darah pada website.';

        return [
            'id' => 'donor_darah_sukarela',
            'category' => 'donor_darah',
            'title' => 'Aksi Donor Darah Sukarela & Syarat Pendonor',
            'keywords' => ['donor darah', 'donor', 'darah', 'syarat donor', 'stok darah', 'golongan darah', 'transfusi', 'utd pmi', 'hemoglobin', 'tensi'],
            'content' => $content,
        ];
    }

    /**
     * Topik 13: Galeri Kegiatan
     */
    protected function buildGalleryTopic(): array
    {
        return [
            'id' => 'galeri_dokumentasi',
            'category' => 'galeri',
            'title' => 'Galeri & Dokumentasi Kegiatan Lapangan PMR Wira',
            'keywords' => ['galeri', 'foto', 'dokumentasi', 'album', 'kegiatan lapangan', 'foto diklatsar', 'foto latgab'],
            'content' => 'Dokumentasi visual kegiatan resmi PMR Wira SMAN 1 Ciawi tersimpan rapi di menu Galeri website: mencakup album Diklatsar ruang dan lapangan, Latihan Gabungan (Latgab) se-Bogor Raya, Donor Darah Kampus, Simulasi Mitigasi Gempa Bumi, Pelantikan Syal Kuning, dan Pengabdian Medis Upacara Bendera.',
        ];
    }

    /**
     * Topik 14: Popup Banner Pengumuman Aktif
     */
    protected function buildPopupBannerTopic(): array
    {
        $bannerText = 'Saat ini ada informasi pengumuman dan publikasi agenda resmi yang dapat dilihat pada popup banner beranda website.';
        try {
            if (Schema::hasTable('popup_banners')) {
                $banner = PopupBanner::where('is_active', true)->first();
                if ($banner) {
                    $bannerText = "Pengumuman Aktif Beranda: {$banner->title}. Link: {$banner->target_url}. Tanggal Tayang: {$banner->start_date} s.d. {$banner->end_date}.";
                }
            }
        } catch (\Throwable $e) {
        }

        return [
            'id' => 'popup_banner_pengumuman',
            'category' => 'pengumuman',
            'title' => 'Pengumuman Banner Pop-up Beranda Website',
            'keywords' => ['banner', 'popup', 'pengumuman', 'iklan', 'berita penting', 'beranda'],
            'content' => $bannerText,
        ];
    }

    /**
     * Topik 15: Kurikulum Standar Kepalangmerahan PMI & KSR
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildCurriculumTopics(): array
    {
        return [
            [
                'id' => 'sejarah_kepalangmerahan',
                'category' => 'kurikulum_pmi',
                'title' => 'Sejarah Gerakan Palang Merah Internasional & PMI',
                'keywords' => ['sejarah', 'henry dunant', 'solferino', 'komite lima', 'icrc', 'konvensi jenewa', 'mohammad hatta', 'sejarah pmi', '17 september 1945', 'bapak palang merah'],
                'content' => 'Sejarah Palang Merah Internasional diprakarsai oleh Jean Henry Dunant (lahir 8 Mei 1828) pasca Pertempuran Solferino (1859). Ia menulis buku "Un Souvenir de Solferino" (1862) yang melahirkan Komite Lima / ICRC (1863) dan Konvensi Jenewa I (1864). Di Indonesia, PMI dibentuk pada 17 September 1945 atas instruksi Presiden Soekarno dan dipimpin pertama kali oleh Drs. Mohammad Hatta. Semboyan: "Siamo Tutti Fratelli" (Kita Semua Bersaudara).',
            ],
            [
                'id' => 'tujuh_prinsip_pmi',
                'category' => 'kurikulum_pmi',
                'title' => '7 Prinsip Dasar Palang Merah & Bulan Sabit Merah Internasional',
                'keywords' => ['7 prinsip', 'tujuh prinsip', 'prinsip dasar', 'kemanusiaan', 'kesamaan', 'kenetralan', 'kemandirian', 'kesukarelaan', 'kesatuan', 'kesemestaan'],
                'content' => "Tujuh Prinsip Dasar Palang Merah (Wina, 1965):\n1. Kemanusiaan (Humanity)\n2. Kesamaan (Impartiality)\n3. Kenetralan (Neutrality)\n4. Kemandirian (Independence)\n5. Kesukarelaan (Voluntary Service)\n6. Kesatuan (Unity)\n7. Kesemestaan (Universality).",
            ],
            [
                'id' => 'tri_bakti_pmr',
                'category' => 'kurikulum_pmi',
                'title' => 'Tri Bakti Palang Merah Remaja (PMR)',
                'keywords' => ['tri bakti', 'tribakti', 'peer leadership', 'peer support', 'peer educator', 'tugas pmr'],
                'content' => "Tiga Pilar Pengabdian Tri Bakti PMR:\n1. Meningkatkan Keterampilan Hidup Sehat (Peer Leadership): Mempraktikkan PHBS, gizi seimbang, dan menjaga kesehatan lingkungan.\n2. Berkarya dan Berbakti di Masyarakat (Peer Support): Donor darah sukarela, piket medis UKS, dan bakti sosial.\n3. Mempererat Persahabatan Nasional dan Internasional (Peer Educator): Menjalin persaudaraan relawan antar-sekolah dan wilayah.",
            ],
            [
                'id' => 'mars_pmi',
                'category' => 'kurikulum_pmi',
                'title' => 'Mars Palang Merah Indonesia (Ciptaan Mochtar M.)',
                'keywords' => ['mars pmi', 'lagu mars', 'lirik mars', 'mochtar m', 'lagu palang merah'],
                'content' => 'Mars Palang Merah Indonesia diciptakan oleh Mochtar M. Lirik: "Palang Merah Indonesia / Wujud kepedulian nyata / Berbakti tanpa pamrih / Untuk sesama manusia // Tujuh Prinsip Dasar Palang Merah / Menjadi pedoman langkah kita / Kemanusiaan Kesamaan / Kenetralan Kemandirian / Kesukarelaan Kesatuan / Dan Kesemestaan // Maju terus Palang Merah / Mengabdi tuk nusa dan bangsa / Menjunjung tinggi kemanusiaan / Demi kejayaan Indonesia".',
            ],
            [
                'id' => 'p3k_resusitasi_jantung_paru',
                'category' => 'p3k',
                'title' => 'Resusitasi Jantung Paru (RJP / CPR) Standar PMR Wira',
                'keywords' => ['rjp', 'cpr', 'resusitasi jantung paru', 'henti jantung', 'henti napas', 'kompresi dada', '30:2', 'karotis', 'bhd'],
                'content' => "Protokol RJP (Resusitasi Jantung Paru) / CPR:\n1. Amankan 3A (Aman Diri, Aman Pasien, Aman Lingkungan).\n2. Cek respon korban (ASNT/AVPU) dengan tepuk pundak tegas: 'Kak! Bangun, Kak!'.\n3. Call for Help: Panggil tim medis darurat atau Hotline UKS (0857-7024-6918).\n4. Cek nadi karotis di leher dan pernapasan dada korban maksimal 10 detik.\n5. Lakukan Kompresi Dada (30 Kompresi : 2 Napas Buatan):\n   • Posisi tangan di pertengahan tulang dada (setengah bawah sternum).\n   • Kedalaman kompresi 5 - 6 cm (dewasa/remaja).\n   • Kecepatan kompresi 100 - 120 kali/menit (irama Stayin' Alive).\n   • Recoil dada penuh di setiap kompresi.\n6. Evaluasi denyut nadi dan napas tiap 5 siklus (~2 menit). Hentikan jika korban sadar, bantuan medis tiba, atau penolong kelelahan.\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'p3k_tandu_darurat',
                'category' => 'p3k',
                'title' => 'Spesifikasi & Teknik Pembuatan Tandu Darurat PMR Wira',
                'keywords' => ['tandu', 'tandu darurat', 'bambu 225', 'anak tandu', 'ukuran bambu', 'simpul jangkar', 'ikatan palang', 'simpul pangkal', 'tali tandu', 'evakuasi'],
                'content' => "Spesifikasi Tandu Darurat Standar PMR Wira:\n• Bambu Utama: 2 batang panjang 225 cm.\n• Anak Tandu: 2 batang panjang 60 cm.\n• Tali Tandu: 2 rol webbing/nylon panjang masing-masing 13-14 meter.\n• Simpul & Ikatan: Simpul pangkal di awal, ikatan palang pada persilangan bambu utama dan anak tandu, simpul jangkar untuk anyaman badan tandu (jarak antar-anyaman 20-25 cm), diakhiri simpul mati yang kuat.\n• Evakuasi: Diangkat 4-6 penolong, posisi kaki korban di depan pada jalan datar.",
            ],
            [
                'id' => 'p3k_patah_tulang_pembidaian',
                'category' => 'p3k',
                'title' => 'Penanganan Patah Tulang (Fraktur) & Pembidaian Standar PMS',
                'keywords' => ['patah tulang', 'fraktur', 'bidai', 'pembidaian', 'pms', 'spalk', 'mitela'],
                'content' => "Prinsip Pembidaian Patah Tulang:\n1. JANGAN PERNAH meluruskan tulang patah yang bengkok secara paksa.\n2. Papan bidai wajib melewati minimal 2 sendi (satu sendi di atas dan satu sendi di bawah fraktur).\n3. Wajib cek PMS (Pulsa, Motorik, Sensorik) sebelum dan sesudah pembidaian.\n4. Ikat dengan mitela simpul mati di sisi luar papan bidai (bukan di atas titik patah tulang).\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'p3k_luka_bakar',
                'category' => 'p3k',
                'title' => 'Pertolongan Pertama Luka Bakar (Combustio)',
                'keywords' => ['luka bakar', 'combustio', 'kena api', 'air panas', 'melepuh', 'odol', 'mentega'],
                'content' => "Penanganan Luka Bakar:\n1. Guyur segera dengan air bersih mengalir selama 10-20 menit untuk meredam panas jaringan kulit.\n2. DILARANG KERAS mengoleskan pasta gigi/odol, mentega, kecap, minyak goreng, atau es batu langsung.\n3. Lepas cincin, jam, atau pakaian ketat sebelum bengkak.\n4. Tutup dengan kassa steril lembab secara longgar.\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'p3k_pingsan_syok',
                'category' => 'p3k',
                'title' => 'Pertolongan Pertama Pasien Pingsan (Sinkop) & Posisi Syok',
                'keywords' => ['pingsan', 'syok', 'sinkop', 'upacara', 'tidak sadar', 'shock position'],
                'content' => "Penanganan Pasien Pingsan (Sinkop) Saat Upacara:\n1. Bawa ke tempat teduh dan berventilasi baik (Ruang UKS).\n2. Baringkan terlentang di permukaan datar.\n3. Longgarkan pakaian, dasi, ikat pinggang, dan kaus kaki.\n4. Tinggikan kedua kaki setinggi 20-30 cm (posisi syok) agar suplai darah kembali ke otak.\n5. Pastikan jalan napas terbuka dan beri minum manis hangat saat sadar penuh.\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'p3k_kejang_kejang',
                'category' => 'p3k',
                'title' => 'Pertolongan Pertama Pasien Kejang-kejang (Konvulsi / Epilepsi)',
                'keywords' => ['kejang', 'kejang-kejang', 'epilepsi', 'step', 'ayan', 'konvulsi'],
                'content' => "Penanganan Kejang-kejang:\n1. Lindungi kepala korban dengan bantalan lunak (jaket/bantal).\n2. Singkirkan benda keras atau tajam dari sekitarnya.\n3. DILARANG KERAS menahan gerakan kejang dan DILARANG memasukkan benda apa pun (sendok, kayu, jari, air minum) ke mulut korban!\n4. Longgarkan pakaian ketat di sekitar leher.\n5. Setelah kejang reda, posisikan miring (posisi pemulihan) agar lidah tidak menyumbat jalan napas.\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'p3k_tersedak_heimlich',
                'category' => 'p3k',
                'title' => 'Pertolongan Pertama Tersedak (Choking & Heimlich Maneuver)',
                'keywords' => ['tersedak', 'choking', 'heimlich', 'tersedak makanan', 'back blows', 'hentakan perut'],
                'content' => "Penanganan Tersedak:\n1. Bila korban masih batuk keras, dukung untuk terus batuk.\n2. Bila tersedak total (tidak bisa bicara/napas): Berikan 5 tepukan punggung tegas (back blows) di antara tulang belikat.\n3. Lanjutkan 5 hentakan perut (Heimlich Maneuver / abdominal thrusts) di atas pusar ke arah dalam dan atas.\n4. Bila korban tidak sadar, segera baringkan dan lakukan RJP 30:2.\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'p3k_keracunan',
                'category' => 'p3k',
                'title' => 'Pertolongan Pertama Keracunan (Makanan, Kimia Korosif vs Non-Korosif, Gas)',
                'keywords' => ['keracunan', 'racun', 'termakan racun', 'minum racun', 'zat kimia', 'norit', 'korosif'],
                'content' => "Penanganan Korban Keracunan:\n1. Amankan 3A dan bawa korban keracunan gas ke udara terbuka yang segar.\n2. Identifikasi zat racun dan amankan sampel/wadah kemasan.\n3. DILARANG memicu muntah jika racun bersifat KOROSIF (asam/basa kuat, deterjen, bensin, minyak tanah) atau korban tidak sadar.\n4. Jika korban sadar dan racun non-korosif, berikan air putih atau tablet norit (arang aktif).\n5. Posisikan miring jika korban muntah spontan.\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'p3k_gigitan_ular',
                'category' => 'p3k',
                'title' => 'Pertolongan Pertama Gigitan Ular Berbisa (Teknik Imobilisasi Tekan / PIT)',
                'keywords' => ['ular', 'gigitan ular', 'bisa ular', 'dipatok ular', 'imobilisasi', 'sabu', 'tourniquet'],
                'content' => "Penanganan Gigitan Ular Berbisa:\n1. IMOBILISASI TOTAL anggota tubuh yang digigit menggunakan balut tekan perban elastis dan bidai kayu (PIT). Korban harus tenang dan tidak bergerak.\n2. Pertahankan posisi anggota tubuh setinggi atau lebih rendah dari jantung.\n3. DILARANG menghisap bisa, DILARANG menyayat luka, DILARANG memasang tourniquet kencang, DILARANG menempelkan es.\n4. Segera evakuasi ke Rumah Sakit untuk pemberian Serum Anti Bisa Ular (SABU).\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'ksr_triage_bencana',
                'category' => 'kurikulum_ksr',
                'title' => 'Triage Lapangan Bencana PMI: Metode START & 4 Tag Warna',
                'keywords' => ['triage', 'triase', 'metode start', 'tag merah', 'tag kuning', 'tag hijau', 'tag hitam', 'korban massal'],
                'content' => "Triage Bencana Metode START (Simple Triage and Rapid Treatment):\n• TAG MERAH (Prioritas 1 - Gawat Darurat): Ancaman jiwa segera, butuh evakuasi cepat (napas > 30x/menit, CRT > 2 dtk).\n• TAG KUNING (Prioritas 2 - Darurat Tidak Gawat): Cedera berat stabil (patah tulang tertutup, luka bakar tanpa komplikasi jalan napas).\n• TAG HIJAU (Prioritas 3 - Tidak Gawat Tidak Darurat): Korban luka ringan yang bisa berjalan (walking wounded).\n• TAG HITAM (Prioritas 0 - Meninggal Dunia): Korban tidak bernapas setelah pembebasan jalan napas atau luka mematikan.\n🚨 Penting: Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.",
            ],
            [
                'id' => 'ksr_hukum_perikemanusiaan_internasional',
                'category' => 'kurikulum_ksr',
                'title' => 'Hukum Perikemanusiaan Internasional (HPI) & 4 Konvensi Jenewa 1949',
                'keywords' => ['hpi', 'hukum perikemanusiaan', 'hukum humaniter', 'konvensi jenewa', 'kombatan', 'kristal merah'],
                'content' => "Hukum Perikemanusiaan Internasional (HPI):\n• 4 Konvensi Jenewa 1949: I (Korban luka/sakit di darat), II (Korban kapal karam di laut), III (Perlakuan tawanan perang POW), IV (Perlindungan warga sipil).\n• Protokol Tambahan 1977 & 2005 (Lambang Kristal Merah).\n• Prinsip Pembedaan (Distinction): Membedakan tegas kombatan militer dan warga sipil.\n• Perlindungan Mutlak: Rumah sakit, ambulans, dan tenaga medis Palang Merah DILARANG DISERANG.",
            ],
            [
                'id' => 'ksr_manajemen_posko',
                'category' => 'kurikulum_ksr',
                'title' => 'Manajemen Posko Tanggap Darurat Bencana PMI',
                'keywords' => ['posko', 'manajemen posko', 'pos komando', 'dapur umum', 'shelter', 'wash', 'rfl', 'assessment'],
                'content' => "8 Unit Utama Manajemen Posko Tanggap Darurat Bencana PMI:\n1. Pos Komando (Posko): Pengendalian operasi tanggap bencana.\n2. Unit Kaji Cepat (Assessment): Pendataan kerusakan dan kebutuhan penyintas.\n3. Pos Pelayanan Medis Lapangan: Pertolongan pertama dan rujukan.\n4. Unit Dapur Umum (DU): Distribusi makanan bergizi.\n5. Unit Logistik & Gudang: Distribusi bantuan adil dan terdata.\n6. Unit Shelter: Tenda pengungsian sementara yang aman.\n7. Unit WASH: Pasokan air bersih dan sanitasi higienis.\n8. Unit RFL (Restoring Family Links): Pemulihan kontak keluarga yang terpisah.",
            ],
        ];
    }

    /**
     * Topik Program Kerja 5 Bidang PMR Wira SMAN 1 Ciawi (Dinamis dari Database & Default Resmi).
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildBidangWorkProgramsTopics(): array
    {
        $bidangDefs = [
            'markas' => [
                'id' => 'program_kerja_bidang_markas',
                'label' => 'Markas',
                'fullLabel' => 'Markas & Logistik UKS',
                'keywords' => ['program kerja markas', 'proker markas', 'tugas pokok markas', 'bidang markas', 'logistik uks', 'program kerja logistik', 'tugas markas'],
                'default' => "1. Pengelolaan dan inventarisasi obat-obatan serta tandu darurat UKS\n2. Pemeliharaan kebersihan, kenyamanan, dan kesiapan ruang markas PMR\n3. Pengadaan logistik medis darurat dan perawatan peralatan medis (spalk, bidai, mitela)\n4. Pengaturan jadwal piket harian markas dan siaga operasional sekolah",
            ],
            'pelayanan' => [
                'id' => 'program_kerja_bidang_pelayanan',
                'label' => 'Pelayanan',
                'fullLabel' => 'Pelayanan Medis & Donor Darah',
                'keywords' => ['program kerja pelayanan', 'proker pelayanan', 'tugas pokok pelayanan', 'bidang pelayanan', 'pelayanan medis', 'donor darah bidang pelayanan', 'tugas pelayanan'],
                'default' => "1. Penyiagaan tim & posko medis darurat pada upacara bendera dan event olahraga sekolah\n2. Pelaksanaan aksi donor darah sukarela bersama UDD PMI Kab. Bogor\n3. Pelayanan kesehatan remaja, posyandu remaja, dan pembagian Tablet Tambah Darah (TTD)\n4. Bakti sosial kemanusiaan, pemeriksaan tensi berkala, dan kepedulian lingkungan warga sekitar sekolah",
            ],
            'diklat' => [
                'id' => 'program_kerja_bidang_diklat',
                'label' => 'Diklat',
                'fullLabel' => 'Pendidikan & Pelatihan (Diklat)',
                'keywords' => ['program kerja diklat', 'proker diklat', 'tugas pokok diklat', 'bidang diklat', 'pelatihan diklat', 'pendidikan dan pelatihan', 'tugas diklat'],
                'default' => "1. Latihan rutin mingguan pertolongan pertama (PP), pembidaian, dan evakuasi tandu\n2. Pembekalan materi 7 Prinsip Dasar Palang Merah & Hukum Humaniter Internasional (HPI)\n3. Simulasi mitigasi kesiapsiagaan bencana gempa bumi & evakuasi kebakaran sekolah\n4. Pemusatan latihan kontingen lomba Jumpa Bakti Gembira (JUMBARA) & evaluasi ketangkasan relawan",
            ],
            'humas' => [
                'id' => 'program_kerja_bidang_humas',
                'label' => 'Humas',
                'fullLabel' => 'Hubungan Masyarakat (Humas)',
                'keywords' => ['program kerja humas', 'proker humas', 'tugas pokok humas', 'bidang humas', 'hubungan masyarakat', 'kemitraan humas', 'tugas humas'],
                'default' => "1. Pengelolaan akun media sosial resmi dan dokumentasi seluruh agenda kegiatan PMR\n2. Penerbitan buletin mading berkala, infografis kesehatan, dan konten edukasi medis\n3. Membangun kemitraan strategis dengan PMI Cabang, Puskesmas, sekolah mitra, dan ikatan alumni\n4. Sosialisasi kepalangmerahan saat MPLS dan rekrutmen anggota baru PMR Wira",
            ],
            'kreasi' => [
                'id' => 'program_kerja_bidang_kreasi',
                'label' => 'Kreasi',
                'fullLabel' => 'Media Kreatif & Publikasi (Kreasi)',
                'keywords' => ['program kerja kreasi', 'proker kreasi', 'tugas pokok kreasi', 'bidang kreasi', 'media kreatif', 'publikasi kreasi', 'tugas kreasi'],
                'default' => "1. Produksi media kreatif visual, video edukasi pertolongan pertama, dan podcast kesehatan kemanusiaan\n2. Pameran karya kreasi relawan dan gelar aksi seni peringatan Hari Palang Merah Sedunia\n3. Inovasi pembuatan alat peraga simulasi medis dari bahan daur ulang ramah lingkungan (eco-friendly)\n4. Perancangan merchandise resmi, KTA digital, poster kampanye donor darah, dan desain visual organisasi",
            ],
        ];

        $orgBidangs = collect();
        if (Schema::hasTable('organization_members')) {
            try {
                $orgBidangs = OrganizationMember::with('member')->where('level', 5)->where('is_active', true)->get();
            } catch (\Throwable $e) {
            }
        }

        $allBidangSummaries = [];
        $topics = [];

        foreach ($bidangDefs as $key => $b) {
            $orgMatch = $orgBidangs->first(function ($om) use ($b) {
                $pos = strtolower(($om->position ?? '').' '.($om->subtitle ?? '').' '.($om->name ?? ''));

                return str_contains($pos, strtolower($b['label']));
            });

            $ketuaName = null;
            $proker = $b['default'];

            if ($orgMatch) {
                if (! empty($orgMatch->work_program)) {
                    $proker = $orgMatch->work_program;
                }
                if ($orgMatch->member) {
                    $ketuaName = $orgMatch->member->name;
                } elseif (! empty($orgMatch->name)) {
                    $ketuaName = $orgMatch->name;
                }
            }

            if (! $ketuaName && Schema::hasTable('members')) {
                try {
                    $m = Member::where('position', 'like', '%ketua%'.$b['label'].'%')
                        ->orWhere('position', 'like', '%kabid%'.$b['label'].'%')
                        ->where('is_active', true)->first();
                    if ($m) {
                        $ketuaName = $m->name;
                    }
                } catch (\Throwable $e) {
                }
            }

            $ketuaText = $ketuaName ? "Ketua: {$ketuaName}" : 'Ketua: Belum ditetapkan pada sistem';

            $topics[] = [
                'id' => $b['id'],
                'category' => 'program_kerja',
                'title' => "Program Kerja Bidang {$b['fullLabel']}",
                'keywords' => $b['keywords'],
                'content' => "Program Kerja Bidang {$b['fullLabel']} PMR Wira SMAN 1 Ciawi (Masa Bakti Ragana Dwi Pantara 2026/2027):\n"
                    ."{$ketuaText}\n\n"
                    ."Rincian Program Kerja:\n{$proker}",
            ];

            $allBidangSummaries[] = "• Bidang {$b['fullLabel']} ({$ketuaText}):\n{$proker}";
        }

        $topics[] = [
            'id' => 'program_kerja_seluruh_bidang',
            'category' => 'program_kerja',
            'title' => 'Program Kerja 5 Bidang PMR Wira SMAN 1 Ciawi (Markas, Pelayanan, Diklat, Humas, Kreasi)',
            'keywords' => [
                'program kerja', 'proker', 'program kerja bidang', 'proker bidang', 'program kerja masing-masing bidang',
                'tugas pokok bidang', 'rencana kerja bidang', 'agenda kerja bidang', 'program kerja seluruh bidang',
                '5 bidang', 'program kerja markas', 'program kerja pelayanan', 'program kerja diklat', 'program kerja humas', 'program kerja kreasi',
                'tugas masing-masing bidang',
            ],
            'content' => "Daftar Program Kerja 5 Bidang Kepengurusan PMR Wira SMAN 1 Ciawi (Masa Bakti Ragana Dwi Pantara 2026/2027):\n\n"
                .implode("\n\n", $allBidangSummaries),
        ];

        return $topics;
    }
}
