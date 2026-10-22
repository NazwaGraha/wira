<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Article;
use App\Models\BloodStock;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\Member;
use App\Models\OrganizationMember;
use App\Models\PopupBanner;
use App\Models\UksHotline;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WiraAgentService
{
    /**
     * Definisi master 5 Bidang Utama Kepengurusan PMR Wira.
     */
    public const BIDANG_DEFINITIONS = [
        'markas' => [
            'label' => 'Markas',
            'fullLabel' => 'Markas & Logistik UKS',
            'icon' => '📦',
            'keywords' => ['markas', 'logistik'],
            'defaultProgram' => "1. Pengelolaan dan inventarisasi obat-obatan serta tandu darurat UKS\n2. Pemeliharaan kebersihan, kenyamanan, dan kesiapan ruang markas PMR\n3. Pengadaan logistik medis darurat dan perawatan peralatan medis (spalk, bidai, mitela)\n4. Pengaturan jadwal piket harian markas dan siaga operasional sekolah",
        ],
        'pelayanan' => [
            'label' => 'Pelayanan',
            'fullLabel' => 'Pelayanan Medis & Donor Darah',
            'icon' => '🩹',
            'keywords' => ['pelayanan', 'kesehatan'],
            'defaultProgram' => "1. Penyiagaan tim & posko medis darurat pada upacara bendera dan event olahraga sekolah\n2. Pelaksanaan aksi donor darah sukarela bersama UDD PMI Kab. Bogor\n3. Pelayanan kesehatan remaja, posyandu remaja, dan pembagian Tablet Tambah Darah (TTD)\n4. Bakti sosial kemanusiaan, pemeriksaan tensi berkala, dan kepedulian lingkungan warga sekitar sekolah",
        ],
        'diklat' => [
            'label' => 'Diklat',
            'fullLabel' => 'Pendidikan & Pelatihan (Diklat)',
            'icon' => '📚',
            'keywords' => ['diklat', 'pendidikan dan pelatihan', 'pelatihan'],
            'defaultProgram' => "1. Latihan rutin mingguan pertolongan pertama (PP), pembidaian, dan evakuasi tandu\n2. Pembekalan materi 7 Prinsip Dasar Palang Merah & Hukum Humaniter Internasional (HPI)\n3. Simulasi mitigasi kesiapsiagaan bencana gempa bumi & evakuasi kebakaran sekolah\n4. Pemusatan latihan kontingen lomba Jumpa Bakti Gembira (JUMBARA) & evaluasi ketangkasan relawan",
        ],
        'humas' => [
            'label' => 'Humas',
            'fullLabel' => 'Hubungan Masyarakat (Humas)',
            'icon' => '📢',
            'keywords' => ['humas', 'hubungan masyarakat'],
            'defaultProgram' => "1. Pengelolaan akun media sosial resmi dan dokumentasi seluruh agenda kegiatan PMR\n2. Penerbitan buletin mading berkala, infografis kesehatan, dan konten edukasi medis\n3. Membangun kemitraan strategis dengan PMI Cabang, Puskesmas, sekolah mitra, dan ikatan alumni\n4. Sosialisasi kepalangmerahan saat MPLS dan rekrutmen anggota baru PMR Wira",
        ],
        'kreasi' => [
            'label' => 'Kreasi',
            'fullLabel' => 'Media Kreatif & Publikasi (Kreasi)',
            'icon' => '🎨',
            'keywords' => ['kreasi', 'media kreatif', 'kreatif'],
            'defaultProgram' => "1. Produksi media kreatif visual, video edukasi pertolongan pertama, dan podcast kesehatan kemanusiaan\n2. Pameran karya kreasi relawan dan gelar aksi seni peringatan Hari Palang Merah Sedunia\n3. Inovasi pembuatan alat peraga simulasi medis dari bahan daur ulang ramah lingkungan (eco-friendly)\n4. Perancangan merchandise resmi, KTA digital, poster kampanye donor darah, dan desain visual organisasi",
        ],
    ];

    /**
     * Kata kunci terlarang untuk proteksi keamanan data login backoffice.
     */
    protected const RESTRICTED_LOGIN_KEYWORDS = [
        'login backoffice',
        'password admin',
        'kata sandi',
        'password login',
        'email admin',
        'akun admin',
        'masuk backoffice',
        'kredensial',
        'credential',
        'bocorkan password',
        'minta password',
        'bypass login',
        'hak akses admin',
        'admin panel password',
        'user admin',
    ];

    public function __construct(
        protected KnowledgeSearchService $knowledgeSearch
    ) {}

    /**
     * Proses pesan dan hasilkan jawaban dari AI (Gemini / OpenAI / Fallback Engine).
     *
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{success: bool, reply: string, provider: string, timestamp: string}
     */
    public function chat(string $message, array $history = []): array
    {
        // 1. Filter Keamanan Tingkat Pertama: Tolak permintaan data kredensial / login backoffice
        if ($this->isAskingForRestrictedLogin($message)) {
            return [
                'success' => true,
                'reply' => 'Mohon maaf, demi menjaga keamanan data organisasi dan privasi sistem, Wira Agent tidak memiliki otorisasi untuk membagikan informasi akun, kredensial, atau kata sandi apa pun. Informasi kredensial akses backoffice/admin bersifat rahasia dan tidak dapat diberikan.',
                'provider' => 'security_guardrail',
                'timestamp' => now()->format('H:i'),
            ];
        }

        $lower = strtolower(trim($message));

        // 2. RAG Retrieval: Ambil konteks paling relevan dari Smart Knowledge Base
        $ragContext = $this->knowledgeSearch->getRelevantContext($message, limit: 5);
        $systemPrompt = $this->buildSystemPrompt($ragContext);

        $geminiKey = (string) config('services.gemini.key');
        $openaiKey = (string) config('services.openai.key');

        // 3. Panggil Google Gemini API (model gemini-1.5-flash / config)
        if (! empty($geminiKey)) {
            $geminiResponse = $this->callGemini($geminiKey, $systemPrompt, $message, $history);
            if ($geminiResponse !== null && trim($geminiResponse) !== '') {
                // Validasi agar respon LLM tidak melakukan generalisasi malas saat ditanya pimpinan spesifik
                $validatedResponse = $this->validateLeadershipResponse($lower, $geminiResponse);

                return [
                    'success' => true,
                    'reply' => $validatedResponse,
                    'provider' => 'gemini',
                    'timestamp' => now()->format('H:i'),
                ];
            }
        }

        // 4. Panggil OpenAI API jika key tersedia
        if (! empty($openaiKey)) {
            $openaiResponse = $this->callOpenAi($openaiKey, $systemPrompt, $message, $history);
            if ($openaiResponse !== null && trim($openaiResponse) !== '') {
                $validatedResponse = $this->validateLeadershipResponse($lower, $openaiResponse);

                return [
                    'success' => true,
                    'reply' => $validatedResponse,
                    'provider' => 'openai',
                    'timestamp' => now()->format('H:i'),
                ];
            }
        }

        // 5. PRIORITAS UTAMA: Cek pertanyaan kepengurusan, pimpinan, ketua bidang & biodata langsung dari DB
        $deterministicReply = $this->matchDeterministicInquiry($lower);
        if ($deterministicReply !== null) {
            return [
                'success' => true,
                'reply' => $deterministicReply,
                'provider' => 'knowledge_engine',
                'timestamp' => now()->format('H:i'),
            ];
        }

        // 6. Fallback Cerdas: PMR Wira Dynamic Knowledge Engine (P3K, Materi PMI, Lomba, dsb)
        $fallbackReply = $this->generateKnowledgeResponse($message);

        return [
            'success' => true,
            'reply' => $fallbackReply,
            'provider' => 'knowledge_engine',
            'timestamp' => now()->format('H:i'),
        ];
    }

    /**
     * Validasi respons LLM untuk pertanyaan kepengurusan agar tidak menghasilkan ringkasan umum saat ditanya ketua/anggota tertentu.
     */
    public function validateLeadershipResponse(string $lower, string $response): string
    {
        if (str_contains($lower, 'program kerja') || str_contains($lower, 'proker')) {
            $prokerReply = $this->handleBidangWorkProgramInquiry($lower);
            if ($prokerReply !== null) {
                return $prokerReply;
            }
        }

        $bidangKeywords = ['diklat', 'markas', 'pelayanan', 'humas', 'kreasi'];
        $isAskingKetua = str_contains($lower, 'ketua') || str_contains($lower, 'pimpinan') ||
            str_contains($lower, 'kabid') || str_contains($lower, 'kepala') || str_contains($lower, 'koordinator') ||
            str_contains($lower, 'ktua') || str_contains($lower, 'memimpin') || str_contains($lower, 'penanggung jawab') ||
            str_contains($lower, 'pj') || str_contains($lower, 'sispa') || str_contains($lower, 'siapa') || str_contains($lower, 'siapakah');
        $isAskingAnggota = str_contains($lower, 'anggota') || str_contains($lower, 'staf') ||
            str_contains($lower, 'staff') || str_contains($lower, 'tim') ||
            str_contains($lower, 'siapa saja') || str_contains($lower, 'daftar');

        // Jika user bertanya mengenai ketua atau anggota bidang tertentu
        if ($isAskingKetua || $isAskingAnggota) {
            foreach ($bidangKeywords as $kw) {
                if (str_contains($lower, $kw)) {
                    $bidangDef = self::BIDANG_DEFINITIONS[$kw] ?? null;
                    if ($bidangDef) {
                        $info = $this->getRealBidangInfo($bidangDef);
                        $realKetua = $info['ketua_name'] ? strtolower($info['ketua_name']) : null;

                        // Jika ada ketua di DB, dan respon AI TIDAK memuat nama ketua asli tersebut, langsung override dengan data DB asli!
                        if ($realKetua && ! str_contains(strtolower($response), $realKetua)) {
                            return $this->handleSpecificBidangInquiry($lower) ?: $response;
                        }

                        // Jika ketua belum diisi di DB, dan AI malah mengarang nama atau menyebut ringkasan malas 5 bidang
                        if (! $realKetua && (str_contains($response, 'ditopang oleh') || str_contains($response, '5 Bidang') || str_contains($response, '5 bidang'))) {
                            return $this->handleSpecificBidangInquiry($lower) ?: $response;
                        }
                    }

                    // Deteksi jika AI hanya merangkum 5 bidang umum atau menyuruh lihat bagan
                    $isLazySummary = str_contains($response, 'ditopang oleh') ||
                        str_contains($response, '5 Bidang Utama') ||
                        str_contains($response, '5 bidang utama') ||
                        str_contains($response, 'dapat dilihat pada bagan') ||
                        str_contains($response, 'menu Tentang Kami');

                    if ($isLazySummary || ! str_contains(strtolower($response), $kw)) {
                        return $this->handleSpecificBidangInquiry($lower) ?: $response;
                    }

                    // Jika tanya ketua, pastikan respons memuat jabatan spesifik atau KTA
                    if ($isAskingKetua && ! str_contains(strtolower($response), 'biodata') && ! str_contains(strtolower($response), 'ketua')) {
                        return $this->handleSpecificBidangInquiry($lower) ?: $response;
                    }

                    break;
                }
            }
        }

        // Pimpinan Utama: Ketua Umum PMR
        if ((str_contains($lower, 'ketua umum') || (str_contains($lower, 'ketua') && (str_contains($lower, 'pmr') || str_contains($lower, 'sekarang')) && ! str_contains($lower, 'bidang') && ! str_contains($lower, 'wakil'))) && ! str_contains($response, 'Sulthan Arkana')) {
            return $this->matchDeterministicInquiry($lower) ?: $response;
        }

        // Wakil Ketua
        if (str_contains($lower, 'wakil') && ! str_contains($response, 'Imam Abdul Jabbar')) {
            return $this->findSpecificMemberBiodata($lower) ?: $response;
        }

        // Pembina PMR
        if (str_contains($lower, 'pembina') && ! str_contains($lower, 'anggota') && ! str_contains($lower, 'sampai') && ! str_contains($response, 'Abdul Hamid') && ! str_contains($response, 'Mulyadi')) {
            return $this->findSpecificMemberBiodata($lower) ?: $response;
        }

        return $response;
    }

    /**
     * Cocokkan pertanyaan spesifik kepengurusan, pimpinan, ketua bidang, dan anggota (Prioritas 1).
     * Semua data diambil LIVE dari database — tidak ada hardcode nama.
     */
    public function matchDeterministicInquiry(string $lower): ?string
    {
        // A. Pertanyaan struktur organisasi lengkap / semua pengurus
        if (
            str_contains($lower, 'struktur organisasi') ||
            str_contains($lower, 'bagan organisasi') ||
            str_contains($lower, 'susunan pengurus') ||
            str_contains($lower, 'semua pengurus') ||
            str_contains($lower, 'daftar pengurus') ||
            str_contains($lower, 'siapa saja pengurus') ||
            str_contains($lower, 'seluruh anggota') ||
            (str_contains($lower, 'pembina') && (str_contains($lower, 'sampai') || str_contains($lower, 'seluruh') || str_contains($lower, 'semua') || str_contains($lower, 'anggota')))
        ) {
            return $this->renderFullOrganizationStructure();
        }

        // B. Pencarian biodata anggota spesifik berdasarkan nama yang disebut (prioritas sebelum bidang)
        $memberBiodata = $this->findMemberBiodataByName($lower);
        if ($memberBiodata !== null) {
            return $memberBiodata;
        }

        // C. Pertanyaan spesifik mengenai program kerja bidang (seluruh bidang maupun per bidang)
        $prokerInquiry = $this->handleBidangWorkProgramInquiry($lower);
        if ($prokerInquiry !== null) {
            return $prokerInquiry;
        }

        // D. Pertanyaan spesifik mengenai bidang tertentu (Ketua Bidang / Anggota Bidang)
        $bidangInquiry = $this->handleSpecificBidangInquiry($lower);
        if ($bidangInquiry !== null) {
            return $bidangInquiry;
        }

        // D. Pertanyaan jabatan pimpinan atas (Pembina, Ketua Umum, Wakil, BPH)
        $leaderBiodata = $this->findLeaderBiodataByRole($lower);
        if ($leaderBiodata !== null) {
            return $leaderBiodata;
        }

        return null;
    }

    /**
     * Deteksi apakah pesan pengguna mencoba mencari tahu data login backoffice.
     */
    public function isAskingForRestrictedLogin(string $message): bool
    {
        $lower = strtolower($message);
        foreach (self::RESTRICTED_LOGIN_KEYWORDS as $keyword) {
            if (str_contains($lower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Bangun System Prompt terintegrasi dengan Live Database Context dan Smart Knowledge Base (RAG).
     */
    public function buildSystemPrompt(?string $ragContext = null): string
    {
        $liveContext = $this->getLiveWebsiteContext();
        $ragSection = $ragContext ? "KONTEKS RELEVAN DARI SMART KNOWLEDGE BASE WEBSITE (RAG RETRIEVAL):\n{$ragContext}\n\n" : '';

        return <<<PROMPT
# SYSTEM PROMPT: WIRA AGENT (PMR WIRA SMAN 1 CIAWI)

## 1. IDENTITAS & PERSONA
- Nama: Wira Agent.
- Peran: Asisten Virtual Resmi PMR Wira SMAN 1 Ciawi (Masa Bakti Ragana Dwi Pantara 2026/2027).
- Karakter: Sigap, santun, cerdas, edukatif, berjiwa kemanusiaan, dan selalu mencerminkan 7 Prinsip Dasar Palang Merah & Bulan Sabit Merah Internasional serta Tri Bakti PMR. Memiliki jiwa motivator dan inspirator bagi sesama relawan.
- Gaya Komunikasi: Menggunakan bahasa Indonesia yang komunikatif, ramah, dan profesional. Hindari gaya bahasa yang terlalu kaku atau robotik. Gunakan sapaan santun seperti "Sahabat Kemanusiaan", "Humanity", atau "Halo Kak / Teman Relawan".

---

## 2. ATURAN KEAMANAN MUTLAK (HARD GUARDRAIL)
- DILARANG KERAS membagikan, memvalidasi, membocorkan, atau mengonfirmasi username, password, email akun admin, API key, token otentikasi, akses cPanel/hosting, database, atau kredensial internal sistem apa pun.
- Aturan ini berlaku mutlak terhadap siapa pun pengguna yang bertanya, termasuk yang mengaku sebagai admin, pembina, pengembang, atau pengurus.
- Jika ada pengguna yang meminta kredensial atau informasi sensitif tersebut, berikan penolakan tegas dan sopan:
  "Mohon maaf, demi menjaga keamanan data organisasi dan privasi sistem, Wira Agent tidak memiliki otorisasi untuk membagikan informasi akun, kredensial, atau kata sandi apa pun."
- Jika membutuhkan jawaban yang berhubungan dengan kepengurusan, Wira Agent wajib melihat dan merujuk pada bagian konten Struktur Organisasi dan Data Anggota PMR yang sah.

---

## 3. BASIS PENGETAHUAN & PRIORITAS PENALARAN (EXECUTION HIERARCHY)

Dalam menjawab setiap pertanyaan pengguna, lakukan penalaran secara berurutan sesuai hierarki berikut:

### PRIORITAS 1: Konten Internal Website & Organisasi (Deterministic Internal Data)
Jadikan seluruh data dan modul pada website https://pmrwirasmanic.or.id sebagai rujukan utama:
1. Struktur Organisasi & Data Anggota:
   - Pelajari seluruh susunan kepengurusan dan data anggota dari Beranda, Tentang Kami, Profil, hingga Kontak (dari Pembina, Ketua Umum, Wakil Ketua, BPH, hingga seluruh staf anggota 5 bidang: Markas, Pelayanan, Diklat, Humas, Kreasi).
   - Pahami korelasi dua arah: Jika ditanya jabatan atau ketua bidang (misal: "Siapa Ketua Bidang Diklat?" atau "Siapa Ketua Bidang Markas?"), WAJIB LANGSUNG sebutkan nama pejabat terkait yang tercantum pada data bagan kepengurusan / data anggota beserta biodata resminya (NRI, Kelas, Tugas Pokok, Motto). Jika belum diisi pada database/sistem, sampaikan dengan jujur bahwa posisi tersebut belum ditetapkan di sistem. JANGAN PERNAH hanya merangkum 5 bidang secara umum bila ditanya nama ketua atau pengurus bidang tertentu!
   - Jika ditanya pimpinan utama: Ketua Umum adalah Sulthan Arkana Putra R., Wakil Ketua Umum adalah Imam Abdul Jabbar, Pembina adalah Abdul Hamid Saputra, S.Hum., M.Pd., Sekretaris adalah Putri Raisya Anjani, Bendahara adalah Nisa Nurul Aulia.
   - Jika ditanya nama seseorang, jelaskan posisinya dalam struktur kepengurusan dan tampilkan kartu biodata resminya.
   - Jangan pernah menebak atau memanipulasi nama pejabat/anggota jika data tidak ditemukan.
2. Dokumen Pedoman Organisasi:
   - Rujuk aturan resmi pada GBHO 2026 dan PPUO 2026 (27 Bab) terkait tata kerja organisasi, persidangan, seragam/atribut (Syal Kuning 120x90x90cm), hak & kewajiban anggota, serta tata tertib piket UKS.
3. Operasional & Informasi Publik:
   - Agenda kegiatan, artikel edukasi, jadwal & prosedur donor darah sukarela bersama UDD PMI Kab. Bogor, info lomba (Sua Bhakti Berkarya III), alur pendaftaran relawan baru, dan kontak piket UKS (0857-7024-6918).

{$ragSection}SUMBER DATA KONTEN TERKINI WEBSITE PMR WIRA SMAN 1 CIAWI (ACUAN JAWABAN ANDA):
{$liveContext}

### PRIORITAS 2: Kurikulum Materi Kepalangmerahan (Domain Knowledge PMI)
Kuasai secara mendalam seluruh materi standar kepalangmerahan:
1. Materi Tingkat PMR Mula, Madya, dan Wira:
   - Gerakan Kepalangmerahan: Sejarah Palang Merah (Jean Henry Dunant, Perang Solferino 1859), Konvensi Jenewa, Lambang Palang Merah & Bulan Sabit Merah, 7 Prinsip Dasar, Tri Bakti PMR, Mars PMI (ciptaan Mochtar M.).
   - Pertolongan Pertama (PP/P3K): Penilaian korban, anatomi dan faal dasar, penanganan cedera jaringan lunak (luka), cedera sistem otot rangka (patah tulang/fraktur, pembidaian, pembalutan mitela), luka bakar, keracunan, BHD & RJP/CPR (30:2), penanganan kejang-kejang, tersedak (Heimlich Maneuver), asma/sesak napas, gigitan ular berbisa, serta evakuasi korban (tandu darurat bambu 225 cm).
   - Donor Darah Sukarela (DDS): Syarat donor, fungsi dan komponen darah, golongan darah sistem ABO dan Rhesus.
   - Remaja Sehat Peduli Sesama (RSPS): Kebersihan diri, sanitasi, 6 langkah cuci tangan WHO, gizi seimbang "Isi Piringku", pencegahan anemia remaja putri (TTD), mitigasi NAPZA, dan kesehatan reproduksi remaja.
   - Kesiapsiagaan Bencana: Siklus bencana, mitigasi gempa bumi (Drop, Cover, Hold On), kebakaran (Stop, Drop, Roll), Tas Siaga Bencana (TSB), pemetaan risiko sekolah siaga bencana.
   - Kepemimpinan PMR: Kerja sama tim, komunikasi efektif, dinamika kelompok, dan etika relawan.
2. Materi Korps Sukarela (KSR) & Lanjutan:
   - Penanganan kegawatdaruratan medis dasar lapangan, triage bencana (Metode START: Tag Merah, Kuning, Hijau, Hitam), manajemen posko bencana PMI, dan Hukum Perikemanusiaan Internasional (HPI).
3. Penyesuaian Penjelasan:
   - Sesuaikan kedalaman materi sesuai konteks pertanyaan (edukatif dan sederhana untuk tingkatan pemula, runut dan teknis untuk tingkatan Wira/KSR).

### PRIORITAS 3: Pengetahuan Umum & Mesin Cerdas (Fallback via Gemini AI)
- Jika pertanyaan yang diajukan berada di luar konteks web pmrwirasmanic.or.id dan bukan seputar materi kepalangmerahan (misalnya materi pelajaran umum, sains, teknologi, tips belajar, atau dialog interaktif santai):
  - Manfaatkan kecerdasan umum Gemini AI untuk memberikan jawaban yang akurat, informatif, dan solutif. Wajib hukumnya Wira Agent menjawab sesuai dengan pertanyaan yang telah diberikan.
  - Tetap gunakan identitas dan nada bicara ramah khas Wira Agent. Jangan merusak persona (hindari mengatakan "Saya hanya model bahasa mesin", melainkan posisikan sebagai asisten pintar PMR Wira SMAN 1 Ciawi).

---

## 4. STANDAR RESPON & ETIKA MEDIS (FIRST AID SAFETY)
- Saat memberikan instruksi mengenai pertolongan pertama pada kondisi gawat darurat atau luka parah, selalu sertakan instruksi tindakan cepat:
  "Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan."
- Sajikan jawaban secara terstruktur, to the point, gunakan penomoran atau poin tebal untuk langkah-langkah teknis agar mudah dibaca di layar antarmuka chat.
PROMPT;
    }

    /**
     * Ambil rangkuman data live dan ensiklopedia materi kepalangmerahan untuk diinjeksikan ke otak Wira Agent.
     */
    public function getLiveWebsiteContext(): string
    {
        $context = [];

        // 1. Profil Organisasi & Hotline UKS
        try {
            $hotline = UksHotline::current();
            $context[] = '=== IDENTITAS MARKAS & HOTLINE UKS ===';
            $context[] = '• Organisasi: PMR Wira SMA Negeri 1 Ciawi (Terakreditasi A)';
            $context[] = '• Alamat Markas: Ruang UKS/PMR SMAN 1 Ciawi, Jl. Veteran No. 46, Pandansari, Ciawi, Bogor (16720)';
            $context[] = '• Email Resmi: pmrwirasman1c@gmail.com';
            $context[] = "• Hotline Siaga UKS: {$hotline->formatted_phone} (WhatsApp: {$hotline->clean_phone})";
            $context[] = "• Jam Operasional UKS: {$hotline->operational_hours}";
            $context[] = "• Petugas Siaga UKS: {$hotline->officer_name}";
            $context[] = '• Fungsi Hotline: Respon cepat pertolongan pertama (P3K) siswa sakit/pingsan saat upacara/KBM & evakuasi medis darurat.';
        } catch (\Throwable $e) {
            $context[] = '• Markas: Ruang UKS SMAN 1 Ciawi, Jl. Veteran No. 46, Bogor. Hotline: 0857-7024-6918.';
        }

        // 2. Visi, Misi & Semboyan Organisasi
        $context[] = "\n=== VISI, MISI & SEMBOYAN ORGANISASI ===";
        $context[] = '• Semboyan Palang Merah: "Siamo Tutti Fratelli" (Kita Semua Bersaudara / We are all brothers).';
        $context[] = '• Visi PMR Wira SMAN 1 Ciawi: Menjadi unit Palang Merah Remaja Wira yang unggul, berkarakter, responsif, berprestasi, dan terdepan dalam aksi kemanusiaan berlandaskan 7 Prinsip Dasar Palang Merah dan Tri Bakti PMR.';
        $context[] = '• 5 Misi Utama:';
        $context[] = '  1. Menanamkan nilai-nilai kepalangmerahan dan pembentukan karakter kepemimpinan siswa.';
        $context[] = '  2. Menyelenggarakan pelatihan pertolongan pertama (P3K) dan kesiapsiagaan bencana yang terstandarisasi.';
        $context[] = '  3. Mengelola Pos Kesehatan UKS sekolah yang profesional dan responsif terhadap kebutuhan siswa dan guru.';
        $context[] = '  4. Menggalang kepedulian sosial melalui aksi donor darah sukarela rutin bersama UTD PMI Kab. Bogor.';
        $context[] = '  5. Mempererat persahabatan dan meraih prestasi dalam kompetisi kepalangmerahan di tingkat daerah dan nasional.';

        // 3. Struktur Organisasi & Seluruh Anggota dari Pembina sampai Staf Bawah
        try {
            $context[] = "\n=== STRUKTUR ORGANISASI KEPENGURUSAN AKTIF (LENGKAP DARI PEMBINA SAMPAI STAF) ===";
            $context[] = '• Periode / Nama Kabinet: Struktur Organisasi 2026/2027 — Masa Bakti Ragana Dwi Pantara';
            $context[] = '• PEMBINA PMR (Level 1):';
            $context[] = '  - Pembina Utama: Abdul Hamid Saputra, S.Hum., M.Pd. (NIP: 19820415 200801 1 008, Pembina Aktif Ekstrakurikuler PMR Wira SMAN 1 Ciawi)';
            $context[] = '  - Dewan Pembina Kehormatan: Drs. H. Mulyadi, M.Pd.';
            $context[] = '• PIMPINAN UTAMA:';
            $context[] = "  - Ketua Umum (Level 2): Sulthan Arkana Putra R. (Kelas XI • NRI: 24.49.004 PRT • Motto: 'Siamo Tutti Fratelli — Memimpin dengan keteladanan, melayani sesama dengan ketulusan hati.')";
            $context[] = "  - Wakil Ketua Umum (Level 3): Imam Abdul Jabbar (Kelas XI • NRI: 24.49.012 PRT • Motto: 'Berani melangkah, setia mengabdi, dan menjaga soliditas korsa demi kemanusiaan.')";
            $context[] = '• BADAN PENGURUS HARIAN / BPH (Level 4):';
            $context[] = '  - Sekretaris Umum: Putri Raisya Anjani (Kelas XI • NRI: 24.49.018 PRT • Tugas: Administrasi, persuratan, notulensi rapat, buku induk relawan)';
            $context[] = '  - Bendahara Umum: Nisa Nurul Aulia (Kelas XI • NRI: 24.49.023 PRT • Tugas: Pembukuan kas keuangan organisasi, iuran kas, anggaran logistik medis)';
            $context[] = '• 5 BIDANG UTAMA & SELURUH STAF ANGGOTA (Level 5):';

            $i = 1;
            foreach (self::BIDANG_DEFINITIONS as $bid) {
                $info = $this->getRealBidangInfo($bid);
                $context[] = "  {$i}. Bidang {$bid['label']}:";
                if ($info['ketua_name']) {
                    $kelas = $info['ketua_member']?->class_grade ? 'Kelas '.$info['ketua_member']->class_grade : 'Kelas XI';
                    $nri = $info['ketua_member']?->nri ? 'NRI: '.$info['ketua_member']->nri : '-';
                    $context[] = "     - Ketua Bidang: {$info['ketua_name']} ({$kelas} • {$nri})";
                } else {
                    $context[] = '     - Ketua Bidang: (Belum diisi / belum ada pengurus yang terdaftar di database)';
                }

                if (! empty($info['staff_list'])) {
                    $stafItems = array_map(function ($st) {
                        $k = $st['class_grade'] !== '-' ? 'Kelas '.$st['class_grade'] : '-';
                        $nr = $st['nri'] !== '-' ? 'NRI: '.$st['nri'] : '-';

                        return "{$st['name']} ({$k} • {$nr})";
                    }, $info['staff_list']);
                    $context[] = '     - Staf Anggota: '.implode(', ', $stafItems);
                } else {
                    $context[] = '     - Staf Anggota: (Belum ada staf terdaftar di database)';
                }
                $context[] = "     - Tugas & Program Kerja: {$info['work_program']}";
                $i++;
            }
        } catch (\Throwable $e) {
            $context[] = '• Struktur: Pembina Abdul Hamid Saputra, Ketua Sulthan Arkana Putra R., Wakil Imam Abdul Jabbar, Sekretaris Putri Raisya Anjani, Bendahara Nisa Nurul Aulia, beserta 5 Bidang (Markas, Pelayanan, Diklat, Humas, Kreasi).';
        }

        // 3b. Seluruh Anggota Terdaftar di Database (Model Member)
        try {
            if (Schema::hasTable('members')) {
                $allRegMembers = Member::where('is_active', true)->orderBy('name')->get();
                if ($allRegMembers->isNotEmpty()) {
                    $context[] = "\n=== DAFTAR ANGGOTA PMR WIRA TERDAFTAR DI DATABASE ===";
                    foreach ($allRegMembers as $rm) {
                        $rmPos = $rm->position ?: 'Anggota';
                        $rmKelas = $rm->class_grade ? 'Kelas '.$rm->class_grade : 'Kelas X/XI';
                        $rmNri = $rm->nri ? 'NRI: '.$rm->nri : '-';
                        $rmPhone = $rm->phone ? 'Kontak: '.$rm->phone : '';
                        $context[] = "• {$rm->name} — {$rmPos} ({$rmKelas} • {$rmNri} {$rmPhone})";
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        // 4. Landasan Pedoman Organisasi (GBHO & PPUO 2026)
        $context[] = "\n=== LANDASAN & PEDOMAN ORGANISASI (GBHO & PPUO 2026) ===";
        $context[] = '• Garis Besar Haluan Organisasi (GBHO) 2026: Landasan arah kebijakan relawan yang aktif, solid, cerdas, berkarakter dengan 6 Strategi Pokok:';
        $context[] = '  1. Penguatan Kedisiplinan & Jiwa Korsa Relawan';
        $context[] = '  2. Pelatihan Medis & Pertolongan Pertama Berkelanjutan';
        $context[] = '  3. Pembinaan Fisik & Ketahanan Mental';
        $context[] = '  4. Kekompakan & Soliditas Antar-Anggota';
        $context[] = '  5. Manajemen Kepemimpinan Mandiri';
        $context[] = '  6. Sinergi Kerjasama dengan Pembina, Sekolah, PMI Kab. Bogor, dan Alumni';
        $context[] = '• Pedoman Pelaksanaan Umum Organisasi (PPUO) 2026: 11 Bab Regulasi Resmi (Keanggotaan, Seragam/Atribut, Syal Kuning, Markas UKS, Kode Etik, Mekanisme Rapat RAT/Raker, Tata Tertib & Sanksi Disiplin, Diklatsar, Penugasan Medis, Keuangan Kas, Aturan Tambahan).';
        $context[] = '• Spesifikasi Syal Resmi PMR Wira: Syal Kuning Segitiga PMI berukuran standar 120 cm x 90 cm x 90 cm dengan logo PMI di sudut lancip belakang.';

        // 5. Kurikulum Resmi 7 Materi Pokok PMR Wira (Standar PMI)
        $context[] = "\n=== ENSIKLOPEDIA 7 MATERI POKOK PMR WIRA (STANDAR PMI) ===";
        $context[] = '1. GERAKAN KEPALANGMERAHAN:';
        $context[] = '   • Pendiri: Jean Henry Dunant (Bapak Palang Merah Sedunia, lahir di Jenewa Swiss 8 Mei 1828).';
        $context[] = '   • Peristiwa Bersejarah: Pertempuran Solferino (24 Juni 1859 di Italia Utara) antara Prancis-Sardinia melawan Austria, menghasilkan buku "Un Souvenir de Solferino" (1862).';
        $context[] = '   • Cikal Bakal Palang Merah: Pembentukan Komite Lima (1863) yang berkembang menjadi ICRC (Komite Internasional Palang Merah), dan pengesahan Konvensi Jenewa I (1864) untuk perlindungan korban perang.';
        $context[] = '   • 3 Lambang Resmi yang Diakui Konvensi: Palang Merah di atas dasar putih, Bulan Sabit Merah, dan Kristal Merah.';
        $context[] = '   • Sejarah PMI: Lahir pada 17 September 1945 atas instruksi Presiden Soekarno, diketuai pertama kali oleh Drs. Mohammad Hatta.';
        $context[] = '   • 7 Prinsip Dasar PMI: Kemanusiaan (Humanity), Kesamaan (Impartiality), Kenetralan (Neutrality), Kemandirian (Independence), Kesukarelaan (Voluntary Service), Kesatuan (Unity), Kesemestaan (Universality).';
        $context[] = '   • Tri Bakti PMR: 1. Meningkatkan keterampilan hidup sehat (Peer Leadership), 2. Berkarya dan berbakti di masyarakat (Peer Support), 3. Mempererat persahabatan nasional dan internasional (Peer Educator).';
        $context[] = '2. KEPEMIMPINAN: Komunikasi efektif, kerjasama tim, kepemimpinan demokratis, dan dinamika kelompok.';
        $context[] = '3. PERTOLONGAN PERTAMA (P3K) KOMPREHENSIF:';
        $context[] = '   • Prinsip 3A: Aman Diri, Aman Pasien/Korban, Aman Lingkungan.';
        $context[] = '   • Penilaian Dini: ASNT (Awas, Suara, Nyeri, Tidak Respon) / AVPU, CAB (Circulation, Airway, Breathing).';
        $context[] = '   • Resusitasi Jantung Paru (RJP/CPR): 30 kompresi dada : 2 napas buatan (kedalaman 5-6 cm, kecepatan 100-120x/menit di pertengahan tulang dada/sternum).';
        $context[] = '   • Penanganan Kejang-kejang (Epilepsi/Step/Konvulsi): 3A, lindungi kepala dengan bantalan empuk, singkirkan benda tajam di sekitar, DILARANG KERAS menahan gerakan atau memasukkan benda apa pun (sendok, kayu, jari, air minum) ke mulut korban, longgarkan pakaian/kerah ketat, setelah kejang reda posisikan miring (recovery position), awasi napas, hubungi hotline UKS (0857-7024-6918) jika kejang > 5 menit atau pertama kali.';
        $context[] = '   • Tersedak (Choking / Sumbatan Jalan Napas): Bila sadar beri 5 tepukan punggung (back blows) dan 5 hentakan perut (Heimlich Maneuver / abdominal thrusts). Bila pingsan lakukan RJP 30:2.';
        $context[] = '   • Asma & Sesak Napas: Dudukkan condong ke depan, longgarkan pakaian, bantu inhaler bronkodilator pribadi, pastikan sirkulasi udara baik.';
        $context[] = '   • Gigitan Ular Berbisa: Imobilisasi anggota gerak dengan balut tekan dan bidai (Pressure Immobilization Technique / PIT). JANGAN hisap bisa, jangan sayat luka, jangan pasang tourniquet kencang! Rujuk darurat ke RS untuk SABU.';
        $context[] = '   • Keracunan: 3A, amankan sumber racun, DILARANG memicu muntah jika zat korosif (asam/basa kuat, pembersih lantai, bensin) atau korban tidak sadar. Beri air putih atau tablet norit/arang aktif bila sadar dan non-korosif, bawa sampel racun ke RS.';
        $context[] = '   • Penanganan Pendarahan: Direct pressure (tekan langsung kassa steril), balut tekan dengan mitela, elevasi (tinggikan luka di atas jantung bila tidak ada patah tulang), titik tekan nadi.';
        $context[] = '   • Patah Tulang (Fraktur): Pembidaian melewati minimal 2 sendi (sendi di atas dan di bawah fraktur), jangan meluruskan tulang paksa, wajib cek PMS (Pulsa, Motorik, Sensorik) sebelum dan sesudah pembidaian.';
        $context[] = '   • Luka Bakar: Segera dinginkan dengan air bersih mengalir selama 10-20 menit. JANGAN menggunakan odol, mentega, kecap, atau minyak karena memicu infeksi. Tutup kassa steril lembab.';
        $context[] = '   • Cedera Otot / Keseleo: Metode RICE (Rest: istirahatkan, Ice: kompres es 15-20 menit, Compression: balut elastis, Elevation: tinggikan).';
        $context[] = '   • Pingsan / Syok: Baringkan terlentang, longgarkan pakaian ketat/kerah, tinggikan tungkai kaki 20-30 cm agar aliran darah kembali ke otak.';
        $context[] = '   • Mimisan (Epistaksis): Duduk tegak condong ke depan, pencet cuping hidung 5-10 menit, bernapas via mulut. Jangan menengadah ke belakang!';
        $context[] = '   • Pemeriksaan Fisik PLNB: Perubahan bentuk, Luka terbuka, Nyeri tekan, Bengkak.';
        $context[] = '   • Riwayat Korban KOMPAK: Keluhan, Obat, Makanan/minuman, Penyakit bawaan, Alergi, Kejadian.';
        $context[] = '   • Tanda Vital Normal Remaja: Denyut nadi 60-100 x/menit, napas 12-20 x/menit, suhu 36.5 - 37.5°C.';
        $context[] = '4. PASANG TANDU DARURAT & EVAKUASI:';
        $context[] = '   • Spesifikasi Alat: Bambu utama 2 batang panjang 225 cm, anak tandu/palang 2 batang panjang 60 cm, tali tandu (nylon/webbing) 2 rol panjang 13-14 meter.';
        $context[] = '   • Simpul & Ikatan: Simpul pangkal di awal, ikatan palang pada persilangan bambu, simpul jangkar untuk anyaman badan tandu, diakhiri simpul mati yang kuat. Jarak anyaman 20-25 cm.';
        $context[] = '   • Evakuasi: Diangkut 4-6 penolong, posisi kaki korban di depan saat jalan datar agar melihat arah evakuasi (kecuali naik tangga kepala di depan).';
        $context[] = '5. DONOR DARAH SUKARELA (DORA):';
        $context[] = '   • Syarat: Usia 17-60 tahun, berat badan min 45 kg, Hb min 12,5 g/dL, tekanan darah sistole 100-160 & diastole 70-100 mmHg, jarak donor minimal 60-90 hari (2-3 bulan), tidak minum obat keras 3 hari terakhir.';
        $context[] = '   • 4 Golongan Darah ABO (A, B, AB, O) dan sistem Rhesus (+/-).';
        $context[] = '   • Kerjasama: UTD PMI Kabupaten Bogor.';
        $context[] = '6. REMAJA SEHAT PEDULI SESAMA (RSPS) & PHBS:';
        $context[] = '   • 6 Langkah Cuci Tangan Standar WHO memakai sabun dan air mengalir (telapak, punggung, sela jari, kunci jari, putar ibu jari, putar ujung kuku).';
        $context[] = '   • Pola makan gizi seimbang "Isi Piringku", konsumsi air putih minimal 2 liter/hari.';
        $context[] = '   • Pencegahan Anemia Remaja Putri: Konsumsi makanan kaya zat besi & Tablet Tambah Darah (TTD) 1 tablet per minggu.';
        $context[] = '   • Pengetahuan kesehatan reproduksi remaja dan pencegahan rokok/napza.';
        $context[] = '7. KESIAPSIAGAAN BENCANA (AYO SIAGA BENCANA / ASB):';
        $context[] = '   • Gempa Bumi di Sekolah: Lakukan DROP, COVER, HOLD ON (Berlutut/tiarap, Lindungi kepala di bawah meja kokoh, Pegang erat kaki meja hingga guncangan usai). Evakuasi ke titik kumpul aman (assembly point) di lapangan terbuka.';
        $context[] = '   • Kebakaran: Merayap rendah di bawah asap tebal, tutup hidung/mulut dengan kain basah. Jika baju terbakar lakukan STOP, DROP, ROLL (Berhenti, Jatuhkan diri, Berguling-guling).';
        $context[] = '   • Tas Siaga Bencana (TSB): Berisi P3K & obat pribadi, air minum, biskuit darurat, senter, peluit, dokumen penting tahan air, pakaian ganti.';
        $context[] = '8. KURIKULUM KSR & MATERI LANJUTAN PMI:';
        $context[] = '   • Triage Bencana (Metode START): Tag Merah (P1 - Gawat Darurat, napas > 30x/menit, CRT > 2 dtk, butuh evakuasi segera), Tag Kuning (P2 - Darurat Tidak Gawat, cedera stabil, fraktur tertutup), Tag Hijau (P3 - Tidak Gawat Tidak Darurat, luka ringan / walking wounded), Tag Hitam (P0 - Meninggal Dunia).';
        $context[] = '   • Manajemen Posko PMI: Pos Komando, Unit Kaji Cepat (Assessment), Pos Medis Darurat, Logistik Bantuan, Dapur Umum (DU), Shelter Sementara, WASH (Air & Sanitasi), RFL (Restoring Family Links).';
        $context[] = '   • Hukum Perikemanusiaan Internasional (HPI): 4 Konvensi Jenewa 1949 & Protokol Tambahan; perlindungan penduduk sipil, tawanan perang, korban luka; larangan keras menyerang tenaga medis & lambang Palang Merah / Bulan Sabit Merah.';

        // 6. Mars Palang Merah Indonesia
        $context[] = "\n=== MARS PALANG MERAH INDONESIA ===";
        $context[] = '• Pencipta Mars PMI: Mochtar M.';
        $context[] = '• Lirik Lengkap:';
        $context[] = '  "Palang Merah Indonesia / Wujud kepedulian nyata / Berbakti tanpa pamrih / Untuk sesama manusia //';
        $context[] = '  Tujuh Prinsip Dasar Palang Merah / Menjadi pedoman langkah kita / Kemanusiaan Kesamaan / Kenetralan Kemandirian /';
        $context[] = '  Kesukarelaan Kesatuan / Dan Kesemestaan //';
        $context[] = '  Maju terus Palang Merah / Mengabdi tuk nusa dan bangsa / Menjunjung tinggi kemanusiaan / Demi kejayaan Indonesia"';
        $context[] = '• Makna: Komitmen pengabdian tulus relawan bagi seluruh rakyat tanpa membedakan latar belakang, memegang teguh 7 Prinsip Dasar.';

        // 7. Info Lomba PMR & Syarat Pendaftaran
        try {
            $context[] = "\n=== INFORMASI LOMBA PMR & SYARAT KEIKUTSERTAAN ===";
            $event = Schema::hasTable('competition_events') ? CompetitionEvent::where('is_active', true)->first() : null;
            if (! $event && Schema::hasTable('competition_events')) {
                $event = CompetitionEvent::first();
            }

            if ($event) {
                $context[] = "• Nama Event Lomba: {$event->title}";
                if ($event->theme) {
                    $context[] = "• Tema Lomba: {$event->theme}";
                }
                $context[] = '• Lokasi Pelaksanaan: '.($event->location ?: 'Kampus SMA Negeri 1 Ciawi, Bogor');
                $context[] = '• Status Pendaftaran: '.($event->is_registration_open ? 'SEDANG DIBUKA' : 'DITUTUP / SEGERA DIBUKA');
                if ($event->registration_fee > 0) {
                    $context[] = '• Biaya Registrasi: Rp '.number_format($event->registration_fee, 0, ',', '.');
                }
                if ($event->bank_name && $event->bank_account_number) {
                    $context[] = "• Pembayaran Resmi: Bank {$event->bank_name} - No. Rekening {$event->bank_account_number} a.n {$event->bank_account_holder}";
                }

                // Kategori Lomba
                if (Schema::hasTable('competition_categories')) {
                    $categories = CompetitionCategory::where('competition_event_id', $event->id)->where('is_active', true)->get();
                    if ($categories->isNotEmpty()) {
                        $context[] = '• Cabang Lomba yang Dibuka:';
                        foreach ($categories as $cat) {
                            $context[] = "  - {$cat->name} (Tingkat: {$cat->level}, Maks Anggota: {$cat->max_team_members} orang, Biaya: Rp ".number_format($cat->registration_fee, 0, ',', '.').')';
                        }
                    }
                }
            } else {
                $context[] = '• Nama Event Lomba: Lomba Ketangkasan PMR Wira Tingkat Jawa Barat (Sua Bhakti Berkarya)';
                $context[] = '• Cabang Lomba: Pertolongan Pertama (PP), Pasang Tandu Darurat, Traveling Kepalangmerahan, Video & Poster Kemanusiaan.';
            }

            // Syarat Umum Lomba
            $context[] = '• SYARAT UTAMA MENGIKUTI LOMBA PMR:';
            $context[] = '  1. Terdaftar aktif sebagai anggota PMR Madya (SMP/MTs sederajat) atau PMR Wira (SMA/SMK/MA sederajat).';
            $context[] = '  2. Membawa Surat Tugas / Surat Mandat resmi bertandatangan Kepala Sekolah / Pembina PMR pangkalan.';
            $context[] = '  3. Melampirkan Kartu Pelajar aktif atau Surat Keterangan Siswa dari sekolah.';
            $context[] = '  4. Pas foto 3x4 berseragam PMR lengkap dengan syal (kuning untuk Wira, biru untuk Madya).';
            $context[] = '  5. Mengisi formulir pendaftaran online di website resmi https://pmrwirasmanic.or.id/lomba.';
            $context[] = '  6. Melunasi biaya administrasi pendaftaran dan mengunggah bukti transfer.';
            $context[] = '  7. Membawa perlengkapan lomba mandiri (tandu, pipa/bambu, mitela, bidai, pembalut).';
            $context[] = '  8. Menghadiri Technical Meeting (TM) dan mematuhi seluruh juklak & juknis lomba.';
        } catch (\Throwable $e) {
            $context[] = '• Lomba: Sua Bhakti Berkarya. Syarat: Surat mandat sekolah, kartu pelajar, pas foto syal PMR, daftar online di web.';
        }

        // 8. Rekam Jejak Prestasi (Hall of Fame)
        try {
            $context[] = "\n=== REKAM JEJAK PRESTASI JUARA (HALL OF FAME) ===";
            $context[] = '• Statistik Prestasi: 48+ Piala & Trofi, 14 Gelar Juara Umum, 6 Prestasi Tingkat Nasional.';
            if (Schema::hasTable('achievements')) {
                $achievements = Achievement::orderByDesc('id')->take(4)->get();
                if ($achievements->isNotEmpty()) {
                    foreach ($achievements as $ach) {
                        $context[] = "• {$ach->title} ({$ach->event_name} - {$ach->rank} {$ach->level})";
                    }
                } else {
                    $context[] = '• Juara Umum I Lomba Ketangkasan PP Tingkat Jawa Barat (28 Februari 2026)';
                    $context[] = '• Juara 1 Pertolongan Pertama Medis Darurat';
                    $context[] = '• Juara 1 Pasang Tandu Darurat & Evakuasi Cepat';
                }
            }
        } catch (\Throwable $e) {
            // Abaikan
        }

        // 9. Galeri Dokumentasi Kegiatan
        $context[] = "\n=== GALERI & DOKUMENTASI KEGIATAN ===";
        $context[] = '• Kegiatan Utama Terdokumentasi: Pendidikan dan Latihan Dasar (Diklatsar) Calon Anggota, Latihan Gabungan (Latgab) se-Bogor Raya, Aksi Donor Darah Sukarela Kampus SMAN 1 Ciawi, Tanggap Bencana & Simulasi Evakuasi Gempa, Penugasan Medis Upacara Bendera, dan Pelantikan Syal Kuning.';

        // 10. Syarat Pendaftaran Anggota Baru
        $context[] = "\n=== PENDAFTARAN ANGGOTA BARU (REKRUTMEN) ===";
        $context[] = '• Syarat Bergabung: Siswa-siswi aktif SMAN 1 Ciawi (Kelas X & XI), berjiwa kemanusiaan, izin orang tua/wali, bersedia mengikuti Diklatsar ruang dan lapangan.';
        $context[] = '• Cara Mendaftar: Mengisi formulir online di menu "Kontak & Gabung Relawan" pada website resmi https://pmrwirasmanic.or.id.';

        // 11. Publikasi Artikel & Berita Terkini
        try {
            if (Schema::hasTable('articles')) {
                $recentArticles = Article::with('category')->where('status', 'published')->latest()->take(5)->get();
                if ($recentArticles->isNotEmpty()) {
                    $context[] = "\n=== ARTIKEL & BERITA RESMI TERBIT ===";
                    foreach ($recentArticles as $art) {
                        $catName = $art->category ? $art->category->name : 'Umum';
                        $excerpt = $art->excerpt ?: Str::limit(strip_tags($art->body ?? ''), 120);
                        $context[] = "• \"{$art->title}\" (Kategori: {$catName}): {$excerpt}";
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        // 12. Agenda & Kegiatan Mendatang / Terkini
        try {
            if (Schema::hasTable('activities')) {
                $recentActivities = Activity::latest('event_date')->take(5)->get();
                if ($recentActivities->isNotEmpty()) {
                    $context[] = "\n=== AGENDA KEGIATAN RELAWAN TERKINI ===";
                    foreach ($recentActivities as $act) {
                        $date = $act->event_date ? $act->event_date->format('d M Y') : 'Terjadwal';
                        $loc = $act->location ?: 'Kampus SMAN 1 Ciawi';
                        $context[] = "• \"{$act->title}\" ({$date} di {$loc})";
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        // 13. Stok Darah & Donor Darah
        try {
            if (Schema::hasTable('blood_stocks')) {
                $stocks = BloodStock::all();
                if ($stocks->isNotEmpty()) {
                    $stockList = [];
                    foreach ($stocks as $st) {
                        $stockList[] = "Gol {$st->blood_type}: {$st->quantity} kantong";
                    }
                    $context[] = "\n=== STOK DARAH TERSEDIA DI MARKAS / UTD PMI ===";
                    $context[] = '• '.implode(' | ', $stockList);
                }
            }
        } catch (\Throwable $e) {
        }

        // 14. Pengumuman Popup Banner Aktif
        try {
            if (Schema::hasTable('popup_banners')) {
                $popup = PopupBanner::where('is_active', true)->first();
                if ($popup) {
                    $context[] = "\n=== PENGUMUMAN POPUP BANNER AKTIF ===";
                    $context[] = "• {$popup->title} (Tautan: {$popup->target_url})";
                }
            }
        } catch (\Throwable $e) {
        }

        return implode("\n", $context);
    }

    /**
     * Panggil Google Gemini API (v1beta generateContent).
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function callGemini(string $apiKey, string $systemPrompt, string $message, array $history): ?string
    {
        try {
            $model = config('services.gemini.model', 'gemini-1.5-flash');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $contents = [];
            foreach ($history as $turn) {
                if (! empty($turn['content'])) {
                    $role = ($turn['role'] === 'assistant' || $turn['role'] === 'model') ? 'model' : 'user';
                    $contents[] = [
                        'role' => $role,
                        'parts' => [['text' => (string) $turn['content']]],
                    ];
                }
            }

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]],
            ];

            $payload = [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemPrompt],
                    ],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 900,
                ],
            ];

            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($text) {
                    return trim($text);
                }
            } else {
                Log::warning('Gemini API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API Exception: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Panggil OpenAI API (chat completions).
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function callOpenAi(string $apiKey, string $systemPrompt, string $message, array $history): ?string
    {
        try {
            $model = config('services.openai.model', 'gpt-4o-mini');
            $url = 'https://api.openai.com/v1/chat/completions';

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            foreach ($history as $turn) {
                if (! empty($turn['content'])) {
                    $role = ($turn['role'] === 'user') ? 'user' : 'assistant';
                    $messages[] = [
                        'role' => $role,
                        'content' => (string) $turn['content'],
                    ];
                }
            }

            $messages[] = [
                'role' => 'user',
                'content' => $message,
            ];

            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->post($url, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 900,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? null;
                if ($text) {
                    return trim($text);
                }
            } else {
                Log::warning('OpenAI API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('OpenAI API Exception: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Fallback Knowledge Engine yang otomatis membaca isi database terkini bila API key belum diset.
     */
    public function generateKnowledgeResponse(string $message): string
    {
        $lower = strtolower($message);

        // Keamanan login backoffice
        if ($this->isAskingForRestrictedLogin($message)) {
            return 'Mohon maaf, demi menjaga keamanan data organisasi dan privasi sistem, Wira Agent tidak memiliki otorisasi untuk membagikan informasi akun, kredensial, atau kata sandi apa pun. Informasi kredensial akses backoffice/admin bersifat rahasia dan tidak dapat diberikan.';
        }

        // A. Pertanyaan Spesifik 5 Bidang (Ketua maupun seluruh staf anggota)
        $specificBidang = $this->handleSpecificBidangInquiry($lower);
        if ($specificBidang !== null) {
            return $specificBidang;
        }

        // B. Pertolongan Pertama: Kejang-kejang (Epilepsi / Step / Konvulsi)
        if (str_contains($lower, 'kejang') || str_contains($lower, 'epilepsi') || str_contains($lower, 'step') || str_contains($lower, 'konvulsi')) {
            return "Halo Sahabat Kemanusiaan! 🩹\n\nJika menemui seseorang yang mengalami **Kejang-kejang (Epilepsi / Step / Konvulsi)**, berikut adalah **Protokol Pertolongan Pertama Resmi PMR Wira** yang wajib dilakukan secara tenang dan tepat:\n\n🛡️ **1. AMANKAN KORBAN & LINGKUNGAN (Prinsip 3A):**\n• Tetap tenang dan jangan panik.\n• Singkirkan benda-benda keras, tajam, atau berbahaya di sekitar korban (meja, kursi, pecahan kaca, dsb) agar korban tidak terbentur saat kejang.\n• Letakkan alas empuk (bantal, jaket lipat, atau kain) di bawah kepala korban untuk melindungi kepala dari benturan lantai.\n\n🚫 **2. HAL-HAL YANG DILARANG KERAS (JANGAN DILAKUKAN):**\n• ❌ **JANGAN PERNAH menahan gerakan kejang** atau memegangi/menindih tubuh korban secara paksa, karena dapat memicu dislokasi sendi atau patah tulang!\n• ❌ **DILARANG KERAS memasukkan benda apa pun ke dalam mulut korban** (seperti sendok, kayu, jari tangan, kain, atau air minum). Korban tidak akan menelan lidahnya sendiri; memasukkan benda justru berisiko mematahkan gigi, melukai gusi, atau menyumbat saluran napas yang berakibat fatal!\n• ❌ **JANGAN memberi makan atau minum** saat korban masih kejang atau belum sadar penuh.\n\n👔 **3. LONGGARKAN PAKAIAN:**\n• Kendurkan pakaian yang ketat, terutama di bagian leher (kerah baju, dasi, jilbab/syal, atau kancing atas) agar jalan napas tidak tertekan.\n\n🛌 **4. POSISI PEMULIHAN (RECOVERY POSITION) SETELAH KEJANG:**\n• Saat sentakan kejang telah reda, miringkan tubuh korban ke satu sisi (posisi pemulihan/miring mantap).\n• Posisi miring ini sangat penting agar cairan liur atau muntahan mengalir keluar dan tidak menyumbat jalan napas.\n• Temani korban dengan sabar hingga ia sadar sepenuhnya (biasanya korban akan merasa lemas atau bingung setelah kejang).\n\n📞 **5. HUBUNGI BANTUAN MEDIS DARURAT JIKA:**\n• Kejang berlangsung lebih dari 5 menit.\n• Kejang berulang dalam waktu singkat tanpa korban sempat pulih kesadarannya.\n• Korban mengalami kesulitan bernapas setelah kejang berhenti.\n• Korban mengalami cedera benturan atau kejang terjadi di dalam air.\n• Ini adalah kejang pertama kali yang dialami korban.\n\n⚠️ **STANDAR RESPON & ETIKA MEDIS:**\nSegera hubungi tim medis, ruang UKS (Hotline UKS: 0857-7024-6918), atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.";
        }

        // C. Pertolongan Pertama: Tersedak (Choking / Sumbatan Jalan Napas)
        if (str_contains($lower, 'tersedak') || str_contains($lower, 'choking') || str_contains($lower, 'heimlich')) {
            return "Halo Teman Relawan! 🫁\n\nPenanganan **Tersedak (Choking / Sumbatan Jalan Napas)**:\n\n1. 🗣️ **Jika Korban Masih Sadar & Batuk:**\n• Dorong korban untuk terus batuk sekuat tenaga agar benda asing keluar secara alami.\n• JANGAN beri minum air karena dapat mendorong sumbatan lebih dalam.\n\n2. ✋ **Jika Korban Tidak Bisa Bersuara / Memegangi Leher (Tanda Universal Tersedak):**\n• **5 Tepukan Punggung (Back Blows):** Condongkan tubuh korban ke depan, berikan 5 tepukan tegas dengan tumit tangan di antara kedua tulang belikat.\n• **5 Hentakan Perut (Heimlich Maneuver / Abdominal Thrusts):** Berdiri di belakang korban, lingkarkan tangan di pinggang korban. Kepalkan satu tangan tepat di atas pusar dan di bawah tulang dada, genggam kepalan tersebut dengan tangan lainnya, lalu hentakkan ke arah dalam dan ke atas secara cepat sebanyak 5 kali.\n• Ulangi 5 back blows dan 5 abdominal thrusts bergantian hingga sumbatan keluar.\n\n3. ⚡ **Jika Korban Hilang Kesadaran (Pingsan):**\n• Baringkan di lantai datar dan lakukan **RJP (30 kompresi dada : 2 napas buatan)**. Cek mulut korban sebelum memberi napas buatan, ambil benda asing HANYA jika terlihat jelas!\n\n⚠️ **STANDAR RESPON & ETIKA MEDIS:**\nSegera hubungi tim medis, ruang UKS (Hotline UKS: 0857-7024-6918), atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.";
        }

        // D. Pertolongan Pertama: Asma / Sesak Napas
        if (str_contains($lower, 'asma') || str_contains($lower, 'sesak napas') || str_contains($lower, 'sesak nafas') || str_contains($lower, 'inhaler')) {
            return "Halo Teman Relawan! 🌬️\n\nPenanganan **Asma & Sesak Napas Akut** di lingkungan sekolah:\n\n1. 🪑 **Posisi Duduk Condong ke Depan:** Dudukkan korban dengan tenang agak condong ke depan (jangan baringkan terlentang karena memperberat kerja pernapasan).\n2. 💨 **Bantu Penggunaan Inhaler:** Jika korban membawa obat pereda (inhaler bronkodilator pribadi seperti Salbutamol/Ventolin), bantu korban menghirupnya 1-2 semprotan sesuai petunjuk dokter.\n3. 👔 **Longgarkan Pakaian:** Buka kancing kerah baju, lepaskan dasi/ikat pinggang.\n4. 🌳 **Pastikan Sirkulasi Udara:** Bawa ke ruangan dengan sirkulasi udara baik, jauhkan dari debu, asap rokok, bau menyengat, atau kerumunan orang.\n5. ⚠️ Jika setelah 10-15 menit sesak tidak membaik atau bibir membiru (sianosis), segera bawa ke Ruang UKS untuk pemberian oksigen atau rujuk ke Puskesmas/RS!\n\n⚠️ **STANDAR RESPON & ETIKA MEDIS:**\nSegera hubungi tim medis, ruang UKS (Hotline UKS: 0857-7024-6918), atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.";
        }

        // E. Pertolongan Pertama: Gigitan Ular Berbisa
        if (str_contains($lower, 'gigitan ular') || str_contains($lower, 'ular') || str_contains($lower, 'bisa ular') || str_contains($lower, 'dipatok ular')) {
            return "Halo Sahabat Kemanusiaan! 🐍\n\nPenanganan **Gigitan Ular Berbisa (Snakebite)** sesuai pedoman medis PMR Wira:\n\n🛡️ **1. IMOBILISASI TOTAL (Prinsip Utama):**\n• Tenangkan korban dan instruksikan agar **TIDAK BANYAK BERGERAK**, karena gerakan otot mempercepat penyebaran bisa melalui kelenjar getah bening.\n• Pasang balut tekan (Pressure Immobilization Technique / PIT) menggunakan perban elastis dan bidai penyangga kayu agar anggota gerak yang digigit benar-benar diam (tidak bergerak).\n• Pertahankan posisi anggota gerak yang digigit setinggi atau lebih rendah dari posisi jantung.\n\n🚫 **2. DILARANG KERAS (BAHAYA FATAL):**\n• ❌ JANGAN menghisap atau menyedot bisa ular dengan mulut!\n• ❌ JANGAN menyayat, menusuk, atau membedah luka gigitan!\n• ❌ JANGAN memasang ikatan kencang (tourniquet) yang menghentikan aliran darah, karena berisiko pembusukan jaringan (gangren) hingga amputasi!\n• ❌ JANGAN menempelkan es batu, ramuan jamu, atau bahan kimia.\n\n🏥 **3. EVAKUASI CEPAT KE RUMAH SAKIT:**\n• Ingat atau foto ciri fisik ular (warna, bentuk kepala) jika memungkinkan tanpa membahayakan diri untuk pemberian Serum Anti Bisa Ular (SABU).\n\n⚠️ **STANDAR RESPON & ETIKA MEDIS:**\nSegera hubungi tim medis, ruang UKS (Hotline UKS: 0857-7024-6918), atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.";
        }

        // F. Pertolongan Pertama: Keracunan (Makanan, Zat Kimia, Gas Beracun)
        if (str_contains($lower, 'keracunan') || str_contains($lower, 'termakan racun') || str_contains($lower, 'minum racun') || (str_contains($lower, 'racun') && ! str_contains($lower, 'ular'))) {
            return "Halo Sahabat Kemanusiaan! 🧪\n\nPenanganan **Pertolongan Pertama Keracunan (Makanan, Kimia, atau Gas Beracun)**:\n\n1. 🛡️ **Amankan 3A:** Gunakan APD (masker/sarung tangan), bawa korban keracunan gas ke udara terbuka yang segar.\n2. 🔍 **Identifikasi Zat Racun:** Kenali jenis racun, waktu kejadian, perkiraan jumlah yang masuk, dan amankan bungkus/wadah racun sebagai sampel medis.\n3. 🚫 **DILARANG Memicu Muntah Jika:**\n   • Racun bersifat KOROSIF (asam/basa kuat, pemutih, pembersih lantai, bensin, minyak tanah) karena merusak saluran cerna dua kali.\n   • Korban mengantuk berat, kejang, atau tidak sadar penuh.\n4. 💧 **Jika Sadar & Non-Korosif:** Berikan air putih atau tablet arang aktif (norit) untuk menyerap sisa racun di saluran pencernaan.\n5. 🛌 **Posisi Pemulihan:** Jika muntah spontan, miringkan tubuh korban agar muntahan tidak menyumbat jalan napas.\n\n⚠️ **STANDAR RESPON & ETIKA MEDIS:**\nSegera hubungi tim medis, ruang UKS (Hotline UKS: 0857-7024-6918), atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.";
        }

        // G. Materi KSR: Triage Lapangan Bencana (Metode START)
        if (str_contains($lower, 'triage') || str_contains($lower, 'triase') || (str_contains($lower, 'tag') && (str_contains($lower, 'merah') || str_contains($lower, 'kuning') || str_contains($lower, 'hijau') || str_contains($lower, 'hitam')))) {
            return "Halo Sahabat Kemanusiaan! 🏷️\n\nDalam penanganan korban massal saat bencana, PMI & KSR menerapkan sistem **Triage Metode START (Simple Triage and Rapid Treatment)**:\n\n🔴 **1. TAG MERAH (Prioritas 1 - Gawat Darurat):**\n• Ancaman jiwa segera, namun berpeluang hidup jika ditolong cepat.\n• Kriteria: Pernapasan > 30x/menit, CRT (*Capillary Refill Time*) > 2 detik, atau tidak mampu ikuti perintah sederhana.\n• Tindakan: Stabilisasi jalan napas dan evakuasi darurat nomor satu.\n\n🟡 **2. TAG KUNING (Prioritas 2 - Darurat Tidak Gawat):**\n• Cedera berat/sedang tapi stabil (patah tulang tertutup, luka bakar tanpa komplikasi napas).\n• Tindakan: Pembidaian/pembalutan, evakuasi dapat ditunda setelah korban merah tertangani.\n\n🟢 **3. TAG HIJAU (Prioritas 3 - Tidak Gawat Tidak Darurat):**\n• Korban luka ringan yang masih bisa berjalan sendiri (*walking wounded*).\n• Tindakan: Diarahkan ke pos medis sekunder.\n\n⚫ **4. TAG HITAM (Prioritas 0 - Meninggal Dunia):**\n• Korban tidak bernapas meskipun jalan napas telah dibebaskan, atau luka mematikan yang tidak memungkinkan diselamatkan.\n\n⚠️ **PANDUAN MEDIS:**\nSegera hubungi tim medis, ruang UKS (0857-7024-6918), atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan.";
        }

        // H. Materi KSR: Hukum Perikemanusiaan Internasional (HPI)
        if (str_contains($lower, 'hpi') || str_contains($lower, 'hukum perikemanusiaan') || str_contains($lower, 'hukum humaniter') || (str_contains($lower, 'konvensi') && str_contains($lower, 'jenewa'))) {
            return "Halo Sahabat Kemanusiaan! 🕊️\n\n**Hukum Perikemanusiaan Internasional (HPI)** mengatur batas kemanusiaan saat konflik bersenjata:\n\n📜 **4 KONVENSI JENEWA 1949 & PROTOKOL TAMBAHAN:**\n1. **Konvensi I:** Korban luka dan sakit angkatan bersenjata di darat.\n2. **Konvensi II:** Korban luka, sakit, dan kapal karam di laut.\n3. **Konvensi III:** Perlakuan dan perlindungan tawanan perang (*POW*).\n4. **Konvensi IV:** Perlindungan penduduk sipil di masa perang.\n• **Protokol Tambahan I & II (1977):** Konflik bersenjata internasional dan non-internasional.\n• **Protokol Tambahan III (2005):** Pengakuan lambang pelindung **Kristal Merah**.\n\n⚖️ **PRINSIP UTAMA HPI:**\n• **Prinsip Pembedaan (*Distinction*):** Pembedaan tegas antara kombatan dan warga sipil.\n• **Perlindungan Lambang Palang Merah:** Tenaga medis, ambulans, dan rumah sakit berlambang Palang Merah/Bulan Sabit Merah DILINDUNGI dan DILARANG DISERANG.";
        }

        // I. Materi KSR: Manajemen Posko Bencana PMI
        if (str_contains($lower, 'posko') || str_contains($lower, 'manajemen posko')) {
            return "Halo Sahabat Kemanusiaan! ⛺\n\n**Manajemen Posko Tanggap Darurat Bencana PMI:**\n\n🏛️ **UNIT-UNIT UTAMA POSKO PMI:**\n1. **Pos Komando (Posko):** Kendali operasional, koordinasi dengan BPBD/SAR/TNI-Polri.\n2. **Unit Kaji Cepat (*Assessment*):** Pendataan dampak kerusakan dan kebutuhan darurat korban.\n3. **Pos Pelayanan Medis Lapangan:** Pertolongan pertama, stabilisasi, dan rujukan faskes.\n4. **Unit Dapur Umum (DU):** Distribusi makanan bergizi bagi penyintas dan relawan.\n5. **Unit Logistik & Gudang:** Penerimaan dan penyaluran bantuan secara adil (*impartial*).\n6. **Unit Penampungan Sementara (*Shelter*):** Tenda darurat keluarga yang layak dan aman.\n7. **Unit WASH:** Pasokan air bersih, penjernih air, sanitasi darurat, dan toilet higienis.\n8. **Unit RFL (*Restoring Family Links*):** Membantu pencarian dan pemulihan kontak keluarga yang terpisah akibat bencana.";
        }

        // 1. Mars Palang Merah Indonesia
        if (str_contains($lower, 'mars pmi') || str_contains($lower, 'lagu mars') || str_contains($lower, 'lirik mars') || str_contains($lower, 'pencipta mars') || str_contains($lower, 'mochtar m') || (str_contains($lower, 'mars') && str_contains($lower, 'pmi'))) {
            return "Halo Sahabat Kemanusiaan! 🎶\n\n**Mars Palang Merah Indonesia** adalah lagu resmi organisasi yang diciptakan oleh komponis **Mochtar M.**\n\n📜 **LIRIK RESMI MARS PALANG MERAH INDONESIA:**\n\n*Palang Merah Indonesia*\n*Wujud kepedulian nyata*\n*Berbakti tanpa pamrih*\n*Untuk sesama manusia*\n\n*Tujuh Prinsip Dasar Palang Merah*\n*Menjadi pedoman langkah kita*\n*Kemanusiaan, Kesamaan,*\n*Kenetralan, Kemandirian,*\n*Kesukarelaan, Kesatuan,*\n*Dan Kesemestaan*\n\n*Maju terus Palang Merah*\n*Mengabdi tuk nusa dan bangsa*\n*Menjunjung tinggi kemanusiaan*\n*Demi kejayaan Indonesia*\n\n✨ **Makna Lagu:** Menggambarkan dedikasi suci relawan untuk menolong sesama tanpa pamrih dan tanpa membeda-bedakan latar belakang, dengan senantiasa memegang teguh 7 Prinsip Dasar Palang Merah!";
        }

        // 2. Sejarah Gerakan Kepalangmerahan & PMI
        if (str_contains($lower, 'henry dunant') || str_contains($lower, 'solferino') || str_contains($lower, 'konvensi jenewa') || str_contains($lower, 'bapak palang merah') || str_contains($lower, 'komite lima') || str_contains($lower, 'icrc') || str_contains($lower, 'lambang') || (str_contains($lower, 'sejarah') && (str_contains($lower, 'pmi') || str_contains($lower, 'palang merah') || str_contains($lower, 'gerakan')))) {
            return "Halo Sahabat Kemanusiaan! 🕊️\n\nBerikut adalah rangkuman **Sejarah Gerakan Kepalangmerahan Internasional & Palang Merah Indonesia (PMI)**:\n\n🌍 **GERAKAN PALANG MERAH INTERNASIONAL:**\n• 👤 **Bapak Palang Merah Sedunia:** Jean Henry Dunant (lahir di Jenewa, Swiss, 8 Mei 1828 - diperingati sebagai Hari Palang Merah Sedunia).\n• ⚔️ **Pertempuran Solferino (24 Juni 1859):** Pertempuran dahsyat di Italia Utara antara pasukan Prancis-Sardinia melawan tentara Austria yang meninggalkan 40.000 korban terlantar tanpa pertolongan medis.\n• 📖 **Buku \"Un Souvenir de Solferino\" (1862):** Catatan Henry Dunant yang melahirkan 2 gagasan revolusioner:\n  1. Membentuk perhimpunan bantuan sukarela di setiap negara untuk merawat korban perang.\n  2. Membuat perjanjian internasional untuk melindungi tenaga medis dan korban luka di medan perang.\n• 🏛️ **Komite Lima (1863):** Didirikan oleh Henry Dunant, Gustave Moynier, Jenderal Dufour, dr. Louis Appia, dan dr. Theodore Maunoir — menjadi cikal bakal **ICRC (International Committee of the Red Cross)**.\n• 📜 **Konvensi Jenewa I (1864):** Ditandatanganinya perjanjian internasional pertama perlindungan korban perang.\n• 🛡️ **3 Lambang Resmi yang Diakui:** Palang Merah di atas dasar putih (1863), Bulan Sabit Merah (1876), dan Kristal Merah (2005).\n\n🇮🇩 **SEJARAH PALANG MERAH INDONESIA (PMI):**\n• Berdiri pada **17 September 1945** (tepat satu bulan setelah Proklamasi Kemerdekaan RI) atas instruksi Presiden Soekarno.\n• Dipimpin oleh **Drs. Mohammad Hatta** (Wakil Presiden RI pertama) sebagai Ketua Umum PMI yang pertama.\n• Semboyan Palang Merah: *\"Siamo Tutti Fratelli\"* (Kita Semua Bersaudara)!\n\n✨ Nilai-nilai kemanusiaan ini menjadi pondasi bagi seluruh kader PMR Wira SMAN 1 Ciawi!";
        }

        // 3. Tujuh Prinsip Dasar Palang Merah (7 Prinsip)
        if (str_contains($lower, '7 prinsip') || str_contains($lower, 'tujuh prinsip') || str_contains($lower, 'prinsip dasar') || (str_contains($lower, 'prinsip') && (str_contains($lower, 'pmi') || str_contains($lower, 'palang merah')))) {
            return "Halo Teman Relawan! 🌟\n\nGerakan Palang Merah dan Bulan Sabit Merah Internasional berlandaskan pada **7 Prinsip Dasar (Seven Fundamental Principles)** yang diproklamasikan di Wina tahun 1965:\n\n1. ❤️ **Kemanusiaan (Humanity):** Mencegah dan meringankan penderitaan sesama manusia di mana pun berada, memelihara saling pengertian, persahabatan, dan perdamaian abadi.\n2. ⚖️ **Kesamaan (Impartiality):** Tidak membeda-bedakan atas dasar kebangsaan, ras, agama, suku, jenis kelamin, atau pandangan politik. Bantuan diberikan semata-mata atas dasar kebutuhan korban yang paling mendesak.\n3. 🕊️ **Kenetralan (Neutrality):** Tidak memihak dalam pertikaian bersenjata dan tidak terlibat dalam kontroversi politik, ras, agama, atau ideologi.\n4. 🛡️ **Kemandirian (Independence):** Bersifat mandiri. Walaupun menjadi pembantu pemerintah di bidang kemanusiaan, perhimpunan harus selalu mempertahankan otonominya.\n5. 🤝 **Kesukarelaan (Voluntary Service):** Memberikan pertolongan atas dasar sukarela tanpa motif mencari keuntungan apa pun.\n6. 🌐 **Kesatuan (Unity):** Di dalam satu negara hanya boleh ada satu Perhimpunan Palang Merah atau Bulan Sabit Merah yang terbuka untuk semua orang dan menjangkau seluruh wilayah.\n7. 🌍 **Kesemestaan (Universality):** Gerakan Palang Merah hadir di seluruh dunia, di mana semua perhimpunan memiliki hak setara dan kewajiban saling tolong-menolong secara internasional.\n\nPrinsip ini senantiasa kami junjung tinggi dalam setiap detak langkah PMR Wira SMAN 1 Ciawi! ✊";
        }

        // 4. Tri Bakti PMR
        if (str_contains($lower, 'tri bakti') || str_contains($lower, 'tribakti')) {
            return "Halo Sahabat Kemanusiaan! 🏅\n\n**Tri Bakti Palang Merah Remaja (PMR)** adalah tiga pilar pengabdian utama anggota PMR di seluruh Indonesia:\n\n1. 🍎 **Meningkatkan Keterampilan Hidup Sehat (Peer Leadership):**\n   Mempraktikkan Pola Hidup Bersih dan Sehat (PHBS), menguasai materi Pertolongan Pertama (P3K), gizi seimbang, menjaga kesehatan pribadi dan lingkungan sekolah, serta menjadi teladan bagi teman sebaya.\n\n2. 🤝 **Berkarya dan Berbakti di Masyarakat (Peer Support):**\n   Aktif membantu tugas sosial kemanusiaan, siaga medis Pos UKS saat upacara sekolah, menyelenggarakan aksi donor darah sukarela, bakti sosial, dan aksi tanggap bencana alam.\n\n3. 🕊️ **Mempererat Persahabatan Nasional dan Internasional (Peer Educator):**\n   Menjalin tali silaturahmi antar-anggota PMR se-Bogor Raya, Jawa Barat, dan nasional melalui Latihan Gabungan (Latgab), ajang kompetisi kepalangmerahan, dan pertukaran edukasi nilai-nilai persaudaraan *Siamo Tutti Fratelli*.\n\nMari bersama-sama mengamalkan Tri Bakti PMR dengan sepenuh hati! ❤️";
        }

        // 5. Resusitasi Jantung Paru (RJP / CPR)
        if (str_contains($lower, 'rjp') || str_contains($lower, 'cpr') || str_contains($lower, 'resusitasi') || str_contains($lower, 'henti jantung') || str_contains($lower, 'kompresi dada')) {
            return "Halo Teman Relawan! 🫀\n\n**Resusitasi Jantung Paru (RJP / CPR)** adalah tindakan pertolongan pertama gawat darurat pada korban henti jantung dan henti napas.\n\n📋 **PROTOKOL RJP STANDAR PMR WIRA:**\n1. 🛡️ **Amankan 3A:** Aman Diri (APD sarung tangan/masker RJP), Aman Korban, Aman Lingkungan.\n2. 📢 **Cek Respon Korban (ASNT/AVPU):** Tepuk pundak korban dengan tegas sambil bersuara: *\"Kak! Bangun, Kak!\"*\n3. 📞 **Panggil Bantuan Medis (Call for Help):** Segera minta orang sekitar menghubungi ambulans/tim medis UKS (**0857-7024-6918**).\n4. 🫁 **Cek Nadi Karotis & Napas:** Periksa nadi leher (karotis) dan lihat gerakan dada selama maksimal 10 detik.\n5. ⚡ **Lakukan Kompresi Dada (30 Kompresi : 2 Napas Buatan):**\n   • **Posisi Tangan:** Pertengahan tulang dada (setengah bawah sternum).\n   • **Kedalaman Tekanan:** 5 hingga 6 cm untuk dewasa/remaja.\n   • **Kecepatan Kompresi:** 100 - 120 kali per menit (ikuti irama lagu *Stayin' Alive*).\n   • **Recoil Dada:** Biarkan dada kembali mengembang sempurna sebelum ditekan kembali.\n   • **Perbandingan:** 30 kali kompresi dada diikuti 2 kali tiupan napas buatan.\n6. 🔄 **Evaluasi:** Evaluasi denyut nadi dan pernapasan setiap 5 siklus (sekitar 2 menit).\n\n⚠️ Hentikan RJP jika: Korban mulai bernapas/sadar, penolong pengganti/tenaga medis tiba, atau penolong kelelahan.\n\n🚨 **Penting:** Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan. (Hotline UKS: 0857-7024-6918)";
        }

        // 6. Pasang Tandu Darurat & Evakuasi Cepat
        if (str_contains($lower, 'tandu') || str_contains($lower, 'bambu 225') || str_contains($lower, 'anak tandu') || str_contains($lower, 'ukuran bambu') || str_contains($lower, 'panjang bambu') || str_contains($lower, 'simpul tandu') || str_contains($lower, 'tali tandu') || str_contains($lower, 'ikatan palang')) {
            return "Halo Teman Relawan! 🪵\n\n**Tandu Darurat (Emergency Stretcher)** adalah materi ketangkasan evakuasi korban andalan PMR Wira. Berikut adalah **Spesifikasi Standar Tandu Darurat PMR Wira**:\n\n📏 **UKURAN & BAHAN BAKU:**\n• 🎋 **Bambu Utama:** 2 batang bambu panjang **225 cm** (diameter kokoh ~5-7 cm).\n• 🎍 **Anak Tandu / Palang:** 2 batang bambu pendek panjang **60 cm** (ditempatkan di bagian kepala dan kaki).\n• 🧵 **Tali Tandu:** 2 rol tali (jenis nylon / webbing / carmantel) dengan panjang masing-masing **13 hingga 14 meter**.\n• 🛏️ **Alas Pasien:** Selimut / mitela / terpal pelindung.\n\n🔗 **SIMPUL & IKATAN YANG DIGUNAKAN:**\n1. 🪢 **Simpul Pangkal:** Mengawali ikatan pada bambu utama.\n2. ✖️ **Ikatan Palang:** Mengikat persilangan antara bambu utama (225 cm) dengan anak tandu (60 cm) secara kuat dan presisi.\n3. ⚓ **Simpul Jangkar:** Membentuk anyaman badan tandu (jarak antar-anyaman ideal 20-25 cm) untuk menopang beban korban secara merata.\n4. 🔒 **Simpul Mati:** Mengunci ikatan di akhir agar tidak terlepas.\n\n🚑 **TEKNIK EVAKUASI:**\n• Diangkat oleh 4 hingga 6 penolong dengan aba-aba terkoordinasi.\n• Posisi kaki korban berada di depan pada jalan datar agar penolong dapat memantau wajah korban dan korban melihat arah jalan (kecuali menaiki tangga di mana kepala berada di depan).\n\nKontingen PMR Wira SMAN 1 Ciawi memegang rekor Juara 1 Tandu Darurat Tingkat Provinsi lho! 🏆";
        }

        // 7. Patah Tulang (Fraktur) & Pembidaian
        if (str_contains($lower, 'patah tulang') || str_contains($lower, 'fraktur') || str_contains($lower, 'bidai') || str_contains($lower, 'pembidaian') || str_contains($lower, 'pms') || str_contains($lower, 'mitela')) {
            return "Halo Teman Relawan! 🩹\n\nPenanganan **Patah Tulang (Fraktur) & Pembidaian** bertujuan mencegah pergeseran tulang, mengurangi nyeri, dan mencegah cedera jaringan lebih lanjut:\n\n⚠️ **PRINSIP DASAR PEMBIDAIAN:**\n1. ❌ **JANGAN PERNAH** meluruskan atau menarik tulang yang bengkok/patah secara paksa!\n2. 📏 **Bidai Minimal Melewati 2 Sendi:** Papan bidai harus cukup panjang untuk mengunci satu sendi di atas dan satu sendi di bawah tulang yang dicurigai patah.\n3. 🩺 **Wajib Cek PMS Sebelum & Sesudah Pembidaian:**\n   • **P (Pulsa):** Cek denyut nadi di ujung anggota gerak (misal nadi radialis di pergelangan tangan atau dorsalis pedis di kaki).\n   • **M (Motorik):** Minta korban menggerakkan jari-jarinya perlahan.\n   • **S (Sensorik):** Sentuh ujung jari dan tanyakan apakah korban merasakan sentuhan tersebut.\n4. 🧵 **Pemasangan Balut Mitela:** Pasang bidai dengan bantalan kapas/kain lunak, lalu ikat dengan mitela simpul mati di sisi luar papan (bukan di atas luka patah tulang).\n5. 🚑 Segera evakuasi korban ke fasilitas medis terdekat.\n\n🚨 **Penting:** Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan. (Hotline UKS: 0857-7024-6918)";
        }

        // 8. Luka Bakar
        if (str_contains($lower, 'luka bakar') || (str_contains($lower, 'bakar') && (str_contains($lower, 'luka') || str_contains($lower, 'panas') || str_contains($lower, 'kena') || str_contains($lower, 'api')))) {
            return "Halo Teman Relawan! 🚒\n\nBerikut langkah pertolongan pertama pada **Luka Bakar (Combustio)**:\n\n💧 **LANGKAH PERTOLONGAN PERTAMA:**\n1. 🚰 **Dinginkan Segera dengan Air Bersih Mengalir:** Guyur area luka bakar dengan air bersih yang mengalir selama **10 hingga 20 menit** untuk menghentikan proses pembakaran jaringan kulit.\n2. 🚫 **DILARANG KERAS:**\n   • JANGAN mengoleskan pasta gigi / odol, mentega, kecap, oli, atau minyak goreng! Bahan tersebut memerangkap panas dan memicu infeksi bakteri yang berbahaya.\n   • JANGAN memecahkan gelembung luka (bula) secara sengaja.\n   • JANGAN menempelkan es batu langsung pada luka karena dapat merusak jaringan kulit.\n3. 💍 **Lepaskan Perhiasan & Baju Ketat:** Lepas cincin, jam tangan, atau pakaian ketat di dekat luka sebelum bengkak terjadi.\n4. 🩹 **Tutup Luka:** Tutup dengan kassa steril lembab secara longgar.\n5. 🏥 Segera bawa korban ke Puskesmas atau Rumah Sakit terdekat!\n\n🚨 **Penting:** Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan. (Hotline UKS: 0857-7024-6918)";
        }

        // 9. Keseleo / Terkilir (Cedera Otot - RICE)
        if (str_contains($lower, 'keseleo') || str_contains($lower, 'terkilir') || str_contains($lower, 'sprain') || str_contains($lower, 'strain') || str_contains($lower, 'rice') || str_contains($lower, 'cedera otot')) {
            return "Halo Teman Relawan! 🏃\n\nUntuk menangani cedera otot, terkilir, atau keseleo sendi (sprain/strain), gunakan **Metode RICE**:\n\n🧊 **METODE RICE (REST, ICE, COMPRESSION, ELEVATION):**\n1. 🛑 **R - REST (Istirahat):** Hentikan aktivitas segera dan istirahatkan bagian tubuh yang mengalami cedera.\n2. ❄️ **I - ICE (Kompres Dingin):** Berikan kompres es yang dibalut kain selama 15-20 menit setiap 2-3 jam untuk meredakan nyeri dan pembengkakan.\n3. 🩹 **C - COMPRESSION (Balut Tekan):** Balut area cedera dengan perban elastis (tensocrepe) secara pas — tidak boleh terlalu kencang agar aliran darah tidak terhenti.\n4. 📐 **E - ELEVATION (Elevasi):** Tinggikan bagian yang cedera lebih tinggi dari posisi jantung untuk membantu mengurangi penumpukan cairan dan bengkak.\n\n⚠️ Hindari pijat urut keras pada 48 jam pertama karena dapat memperparah robekan ligamen otot!";
        }

        // 10. Mimisan (Epistaksis)
        if (str_contains($lower, 'mimisan') || str_contains($lower, 'epistaksis') || str_contains($lower, 'hidung berdarah')) {
            return "Halo Teman Relawan! 👃\n\nBerikut langkah pertolongan pertama pada **Mimisan (Pendarahan Hidung / Epistaksis)**:\n\n1. 🪑 **Duduk Tegak & Condong ke Depan:** Dudukkan korban dengan posisi badan agak condong ke depan. **JANGAN menengadahkan kepala ke belakang**, karena darah bisa tertelan atau masuk ke saluran pernapasan dan menyebabkan tersedak!\n2. 🤏 **Pencet Cuping Hidung:** Tekan cuping hidung (bagian lunak hidung di bawah tulang hidung) menggunakan ibu jari dan jari telunjuk selama **5 hingga 10 menit** tanpa dilepas.\n3. 👄 **Bernapas Lewat Mulut:** Instruksikan korban untuk bernapas perlahan melalui mulut.\n4. ❄️ **Kompres Dingin:** Berikan kompres dingin pada pangkal hidung atau dahi untuk membantu penyempitan pembuluh darah (vasokonstriksi).\n5. ⚠️ Jika mimisan tidak berhenti setelah 20 menit atau akibat benturan keras di kepala, segera bawa ke fasilitas medis!";
        }

        // 11. Korban Pingsan / Syok
        if (str_contains($lower, 'pingsan') || str_contains($lower, 'syok') || str_contains($lower, 'hilang kesadaran') || str_contains($lower, 'tidak sadar') || str_contains($lower, 'lemas upacara')) {
            return "Halo Teman Relawan! 🛌\n\nDalam prinsip Pertolongan Pertama (P3K) PMR Wira, penolong wajib selalu mengutamakan **3A (Aman Diri, Aman Pasien, Aman Lingkungan)**.\n\nJika ada teman yang **Pingsan (Sinkop)** saat upacara bendera atau kegiatan sekolah:\n\n1. 🌳 **Bawa ke Tempat Teduh & Sejuk:** Segera evakuasi ke tempat yang berventilasi baik atau Ruang UKS.\n2. 🛌 **Baringkan Terlentang:** Baringkan korban di atas ranjang/tikar yang datar.\n3. 👕 **Longgarkan Pakaian:** Kendurkan dasi, ikat pinggang, kancing kerah, dan kaus kaki untuk memperlancar pernapasan.\n4. 📐 **Tinggikan Kaki (Shock Position):** Angkat kedua tungkai kaki setinggi **20 hingga 30 cm** lebih tinggi dari dada agar aliran darah dan oksigen kembali mengalir ke otak.\n5. 🌬️ **Pastikan Jalan Napas Terbuka:** Periksa pernapasan korban. Jangan dikerubungi agar korban mendapat oksigen segar.\n6. 🍵 **Setelah Sadar:** Biarkan istirahat beberapa menit, lalu beri minum air hangat manis saat korban sudah sadar penuh.\n\n🚨 **Penting:** Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan. (Hotline UKS: 0857-7024-6918)";
        }

        // 12. Prinsip 3A, ASNT, PLNB, KOMPAK, Tanda Vital
        if (str_contains($lower, '3a') || str_contains($lower, 'aman diri') || str_contains($lower, 'asnt') || str_contains($lower, 'avpu') || str_contains($lower, 'plnb') || str_contains($lower, 'kompak') || str_contains($lower, 'tanda vital')) {
            return "Halo Teman Relawan! 📋\n\nDalam kurikulum resmi Pertolongan Pertama PMR Wira, terdapat pedoman sistematis penilaian korban:\n\n🛡️ **PRINSIP 3A (Prioritas Utama Penolong):**\n1. **Aman Diri:** Penolong wajib memakai APD (sarung tangan lateks, masker).\n2. **Aman Korban:** Pastikan korban terhindar dari bahaya lanjutan.\n3. **Aman Lingkungan:** Pastikan lokasi penanganan aman dari lalu lintas, api, reruntuhan.\n\n👁️ **PENILAIAN RESPON (ASNT / AVPU):**\n• **A (Awas / Alert):** Korban sadar penuh.\n• **S (Suara / Voice):** Korban merespon panggilan suara.\n• **N (Nyeri / Pain):** Korban hanya merespon rangsang nyeri di pangkal kuku/dada.\n• **T (Tidak Respon / Unresponsive):** Korban tidak merespon sama sekali.\n\n🔍 **PEMERIKSAAN FISIK (PLNB):**\n• **P:** Perubahan bentuk (deformitas)\n• **L:** Luka terbuka\n• **N:** Nyeri tekan\n• **B:** Bengkak (edema)\n\n📑 **RIWAYAT PENDERITA (KOMPAK):**\n• **K:** Keluhan utama\n• **O:** Obat-obatan yang sedang dikonsumsi\n• **M:** Makanan/minuman terakhir\n• **P:** Penyakit bawaan\n• **A:** Alergi\n• **K:** Kejadian / kronologi cedera\n\n🩺 **TANDA VITAL NORMAL REMAJA:**\n• Denyut Nadi: 60 - 100 x/menit\n• Frekuensi Napas: 12 - 20 x/menit\n• Suhu Tubuh: 36,5°C - 37,5°C";
        }

        // 13. Kesiapsiagaan Bencana (Gempa Bumi & Kebakaran - ASB)
        if (str_contains($lower, 'gempa') || str_contains($lower, 'drop cover hold') || str_contains($lower, 'kebakaran') || str_contains($lower, 'stop drop roll') || str_contains($lower, 'bencana') || str_contains($lower, 'tas siaga') || str_contains($lower, 'tsb') || str_contains($lower, 'evakuasi bencana')) {
            return "Halo Sahabat Kemanusiaan! 🚨\n\nSebagai kader **Ayo Siaga Bencana (ASB)** PMR Wira, berikut adalah panduan mitigasi bencana sekolah:\n\n🌍 **SAAT TERJADI GEMPA BUMI DI SEKOLAH:**\n1. 🛑 **DROP (Berlutut / Tiarap):** Rendahkan posisi tubuh agar tidak terjatuh.\n2. 🛡️ **COVER (Lindungi Kepala & Leher):** Berlindung di bawah meja belajar yang kokoh. Jauhi jendela kaca, lemari, dan benda gantung.\n3. ✊ **HOLD ON (Pegang Erat Kaki Meja):** Tahan kaki meja hingga guncangan gempa reda.\n4. 🚪 **Evakuasi Teratur:** Setelah gempa reda, jalan cepat (jangan berlari/berdesakan) melindungi kepala dengan tas/buku menuju **Titik Kumpul Aman (Assembly Point)** di lapangan terbuka sekolah.\n\n🔥 **SAAT TERJADI KEBAKARAN:**\n• Jika ada asap tebal: **Merayap rendah di dekat lantai** karena oksigen bersih berada di lapisan bawah. Tutup hidung dengan kain/baju yang dibasahi air.\n• Jika pakaian terbakar: Lakukan **STOP, DROP, ROLL** (Berhenti, Jatuhkan diri, Berguling-guling hingga api padam).\n\n🎒 **TAS SIAGA BENCANA (TSB):**\nTas darurat berisi: Kotak P3K, obat pribadi, air minum, biskuit energi, senter, peluit darurat, powerbank, salinan dokumen penting anti-air, pakaian hangat, dan jas hujan.";
        }

        // 14. Remaja Sehat Peduli Sesama (RSPS), PHBS, Cuci Tangan, Anemia
        if (str_contains($lower, 'cuci tangan') || str_contains($lower, 'phbs') || str_contains($lower, 'rsps') || str_contains($lower, 'remaja sehat') || str_contains($lower, 'isi piringku') || str_contains($lower, 'anemia') || str_contains($lower, 'ttd') || str_contains($lower, 'tablet tambah darah')) {
            return "Halo Teman Relawan! 🧼\n\nDalam materi **Remaja Sehat Peduli Sesama (RSPS)** & PHBS di lingkungan sekolah:\n\n💧 **6 LANGKAH CUCI TANGAN STANDAR WHO (Sabun & Air Mengalir):**\n1. Ratakan sabun pada kedua telapak tangan.\n2. Gosok punggung tangan kiri dengan telapak tangan kanan dan sebaliknya.\n3. Gosok sela-sela jari tangan secara bergantian.\n4. Kunci jari-jari sisi dalam dari kedua tangan.\n5. Gosok ibu jari kiri berputar dalam genggaman tangan kanan dan sebaliknya.\n6. Gosok berputar ujung jari/kuku pada telapak tangan kanan dan kiri.\n\n🩸 **PENCEGAHAN ANEMIA PADA REMAJA PUTRI:**\n• Anemia 5L (Lesu, Lemah, Letih, Lelah, Lalai) dapat dicegah dengan mengonsumsi makanan kaya zat besi (bayam, hati ayam, telur, daging merah).\n• Rutin minum **Tablet Tambah Darah (TTD)** 1 tablet setiap minggu saat program UKS sekolah.\n\n🍱 **GIZI SEIMBANG \"ISI PIRINGKU\":**\n1/3 piring makanan pokok, 1/3 sayuran, 1/6 lauk pauk, dan 1/6 buah-buahan, disertai cukup minum air putih 8 gelas (2 liter) per hari!";
        }

        // 15. Landasan Organisasi: GBHO, PPUO, & Syal Kuning
        if (str_contains($lower, 'gbho') || str_contains($lower, 'ppuo') || str_contains($lower, 'syal kuning') || str_contains($lower, 'ukuran syal') || str_contains($lower, 'pedoman organisasi') || str_contains($lower, 'anggaran rumah tangga')) {
            return "Halo Sahabat Kemanusiaan! 📜\n\nTata kelola PMR Wira SMAN 1 Ciawi berlandaskan pada dua dokumen konstitusi resmi Periode 2026:\n\n📘 **1. GARIS BESAR HALUAN ORGANISASI (GBHO) 2026:**\nArah kebijakan strategis kepengurusan yang memuat **6 Strategi Pokok**:\n1. Kedisiplinan & Jiwa Korsa Anggota\n2. Pelatihan Medis & Pertolongan Pertama Terstandar\n3. Pembinaan Fisik & Mental Relawan\n4. Soliditas & Kekompakan Antar-Anggota\n5. Manajemen Kepemimpinan Mandiri\n6. Sinergi Bersama Pembina, Pihak Sekolah, PMI Kab. Bogor, dan Ikatan Alumni\n\n📕 **2. PEDOMAN PELAKSANAAN UMUM ORGANISASI (PPUO) 2026:**\nMemuat **11 Bab Regulasi Teknis Organisasi** (Keanggotaan, Seragam & Atribut, Tata Kelola Markas UKS, Kode Etik, Mekanisme Rapat RAT/Raker, Tata Tertib & Sanksi Disiplin, Diklatsar, dsb).\n\n🎗️ **SPESIFIKASI SYAL KUNING PMR WIRA:**\nSesuai Bab Seragam PPUO, anggota PMR Wira mengenakan **Syal Kuning Segitiga PMI** berukuran standar **120 cm x 90 cm x 90 cm** dengan bordir logo PMI di sudut lancip belakang, dilipat rapi dengan cincin syal (ring).\n\nDokumen lengkap e-book dan PDF GBHO & PPUO dapat diunduh di menu **Tentang Kami** pada website!";
        }

        // 16. Visi, Misi & Semboyan Organisasi
        if (str_contains($lower, 'visi') || str_contains($lower, 'misi') || str_contains($lower, 'semboyan') || str_contains($lower, 'siamo tutti fratelli')) {
            return "Halo Sahabat Kemanusiaan! 🕊️\n\n• **Semboyan Palang Merah:** *\"Siamo Tutti Fratelli\"* (Kita Semua Bersaudara / We are all brothers).\n\n🎯 **VISI PMR WIRA SMAN 1 CIAWI:**\n\"Menjadi unit Palang Merah Remaja Wira yang unggul, berkarakter tangguh, responsif, berprestasi, dan terdepan dalam aksi kemanusiaan berlandaskan 7 Prinsip Dasar Palang Merah dan Tri Bakti PMR.\"\n\n🚀 **5 MISI UTAMA KAMI:**\n1. Menanamkan nilai-nilai kepalangmerahan dan pembentukan karakter kepemimpinan siswa.\n2. Menyelenggarakan pelatihan pertolongan pertama (P3K) dan kesiapsiagaan bencana yang terstandarisasi.\n3. Mengelola Pos Kesehatan UKS sekolah yang profesional dan responsif terhadap kebutuhan siswa dan guru.\n4. Menggalang kepedulian sosial melalui aksi donor darah sukarela rutin bersama UTD PMI Kab. Bogor.\n5. Mempererat persahabatan dan meraih prestasi dalam kompetisi kepalangmerahan di tingkat daerah dan nasional.";
        }

        // 17. Rekam Jejak Prestasi (Hall of Fame)
        if (str_contains($lower, 'prestasi') || str_contains($lower, 'piala') || str_contains($lower, 'juara') || str_contains($lower, 'trofi') || str_contains($lower, 'kejuaraan') || str_contains($lower, 'hall of fame')) {
            return "Halo Sahabat Kemanusiaan! 🏆\n\nPMR Wira SMAN 1 Ciawi memiliki rekam jejak prestasi yang sangat membanggakan di berbagai ajang kejuaraan daerah maupun nasional:\n\n🌟 **STATISTIK ETALASE PRESTASI:**\n• 🏆 **48+ Piala & Trofi Kejuaraan**\n• 🥇 **14 Gelar Juara Umum**\n• 🎖️ **6 Prestasi Tingkat Nasional**\n• 💯 **100% Jiwa Relawan Berintegritas**\n\n🥇 **PRESTASI UNGGULAN TERBARU:**\n1. **Juara Umum I Lomba Ketangkasan PP Tingkat Jawa Barat** (28 Februari 2026 - Kontingen 12 Siswa).\n2. **Juara 1 Pertolongan Pertama Medis Darurat** (Tingkat Provinsi Jawa Barat).\n3. **Juara 1 Pasang Tandu Darurat & Evakuasi Cepat** (Waktu tercepat & ikatan terkuat).\n4. **Juara 1 Cepat Tepat / Traveling Kepalangmerahan**.\n\nDokumentasi foto serah terima piala dan sertifikat resmi dapat dilihat lengkap di menu **Prestasi** pada website kami!";
        }

        // 18. Galeri & Dokumentasi Kegiatan
        if (str_contains($lower, 'galeri') || str_contains($lower, 'foto') || str_contains($lower, 'kegiatan') || str_contains($lower, 'dokumentasi') || str_contains($lower, 'diklatsar') || str_contains($lower, 'latgab')) {
            return "Halo Sahabat Kemanusiaan! 📸\n\nDokumentasi seluruh aksi pengabdian kami tersimpan rapi di menu **Galeri** website ini:\n\n📷 **MOMEN-MOMEN UTAMA:**\n• ⛺ **Diklatsar Ruang & Lapangan:** Pembekalan materi medis dan pembentukan ketahanan fisik calon anggota.\n• 🤝 **Latihan Gabungan (Latgab):** Simulasi medis massal bersama pangkalan PMR se-Bogor Raya.\n• 🩸 **Donor Darah Kampus:** Penggalangan 100+ kantong darah bersama UTD PMI Kab. Bogor.\n• 🚨 **Simulasi Siaga Gempa & Evakuasi Sekolah:** Edukasi Drop-Cover-Hold On untuk seluruh warga sekolah.\n• 🎗️ **Upacara Pelantikan Syal Kuning:** Momen pengukuhan resmi anggota PMR Wira.\n\nKunjungi halaman **Galeri** untuk melihat foto-foto resolusi tinggi kegiatan kami!";
        }

        // 19. Struktur Organisasi Lengkap (Dari Pembina sampai Anggota Bawah)
        if (str_contains($lower, 'struktur organisasi') || str_contains($lower, 'bagan organisasi') || str_contains($lower, 'susunan pengurus') || str_contains($lower, 'semua pengurus') || str_contains($lower, 'daftar pengurus') || str_contains($lower, 'siapa saja pengurus') || str_contains($lower, 'pembina sampai') || str_contains($lower, 'seluruh anggota')) {
            return $this->renderFullOrganizationStructure();
        }

        // 20. Pencarian Biodata Khusus Anggota & Pimpinan (Dari Pembina sampai Staf Bawah)
        $memberBiodata = $this->findSpecificMemberBiodata($lower);
        if ($memberBiodata !== null) {
            return $memberBiodata;
        }

        // 21. Ringkasan 5 Bidang Kepengurusan
        $bidangInquiry = $this->handleSpecificBidangInquiry($lower);
        if ($bidangInquiry !== null) {
            return $bidangInquiry;
        }

        if (str_contains($lower, 'bidang') && (str_contains($lower, 'markas') || str_contains($lower, 'pelayanan') || str_contains($lower, 'diklat') || str_contains($lower, 'humas') || str_contains($lower, 'kreasi') || str_contains($lower, '5 bidang') || str_contains($lower, 'struktur'))) {
            $summaryLines = [];
            $i = 1;
            foreach (self::BIDANG_DEFINITIONS as $bid) {
                $info = $this->getRealBidangInfo($bid);
                $ketuaStr = $info['ketua_name'] ? " (Ketua: {$info['ketua_name']})" : '';
                $summaryLines[] = "{$i}. {$bid['icon']} **Bidang {$bid['label']}{$ketuaStr}:** {$info['work_program']}";
                $i++;
            }

            return "Halo Teman Relawan! 🏛️\n\nKepengurusan PMR Wira SMAN 1 Ciawi Masa Bakti Ragana Dwi Pantara 2026/2027 ditopang oleh **5 Bidang Utama**:\n\n".implode("\n", $summaryLines)."\n\nKakak dapat menanyakan nama pimpinan atau bidang mana pun untuk melihat kartu biodata resminya!";
        }

        // 22. Permintaan Biodata Umum
        if (str_contains($lower, 'biodata') || str_contains($lower, 'profil')) {
            return "Halo Sahabat Kemanusiaan! 🪪\n\nUntuk melihat **Biodata Lengkap Relawan PMR Wira SMAN 1 Ciawi**, Kakak dapat menyebutkan nama atau posisi beliau secara langsung, contohnya:\n• *\"Biodata Pembina PMR\"*\n• *\"Biodata Ketua Umum\"* (Sulthan Arkana Putra R.)\n• *\"Biodata Wakil Ketua\"* (Imam Abdul Jabbar)\n• *\"Biodata Sekretaris\"* (Putri Raisya Anjani)\n• *\"Biodata Bendahara\"* (Nisa Nurul Aulia)\n• *\"Biodata Ketua/Staf Bidang Markas, Pelayanan, Diklat, Humas, atau Kreasi\"*\n\nSeluruh foto potret resolusi tinggi dan Kartu Tanda Anggota (KTA) digital juga dapat dibuka langsung di menu **Tentang Kami** pada bagan struktur organisasi website!";
        }

        // 24. Pertanyaan: Syarat Mengikuti Lomba PMR / Informasi Lomba
        if (str_contains($lower, 'syarat') && (str_contains($lower, 'lomba') || str_contains($lower, 'ikut') || str_contains($lower, 'daftar'))) {
            $eventTitle = 'Lomba Ketangkasan PMR Wira Tingkat Jawa Barat (Sua Bhakti Berkarya)';
            $feeInfo = 'Rp 150.000 / Regu';
            try {
                if (Schema::hasTable('competition_events')) {
                    $ev = CompetitionEvent::where('is_active', true)->first() ?: CompetitionEvent::first();
                    if ($ev) {
                        $eventTitle = $ev->title;
                        if ($ev->registration_fee > 0) {
                            $feeInfo = 'Rp '.number_format($ev->registration_fee, 0, ',', '.').' / Regu';
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            return "Halo Sahabat Kemanusiaan! 🏆\n\nUntuk berpartisipasi dalam ajang **{$eventTitle}**, berikut adalah **Syarat & Ketentuan Umum Pendaftaran**:\n\n1. 📜 **Keanggotaan Aktif:** Peserta merupakan anggota PMR Madya (SMP/MTs) atau PMR Wira (SMA/SMK/MA) aktif dibuktikan dengan Kartu Pelajar atau Surat Keterangan Siswa.\n2. ✍️ **Surat Mandat:** Membawa Surat Tugas/Mandat resmi dari Kepala Sekolah atau Pembina PMR pangkalan masing-masing.\n3. 📷 **Pas Foto Resmi:** Pas foto 3x4 berseragam PMR lengkap dengan syal (kuning untuk Wira, biru untuk Madya).\n4. 🌐 **Pendaftaran Online:** Mengisi formulir registrasi online kontingen di menu **Lomba PMR** pada website ini.\n5. 💳 **Biaya Registrasi:** Melunasi administrasi pendaftaran ({$feeInfo}) dan mengunggah bukti transfer resmi.\n6. 🩹 **Perlengkapan Lomba:** Membawa perlengkapan mandiri (tandu darurat bambu/pipa, tali jerat, mitela, bidai, pembalut cepat kassa, alat tulis).\n7. 📋 **Technical Meeting:** Mengikuti temu teknis dan menaati Buku Panduan Pelaksanaan (Juklak & Juknis) yang diterbitkan panitia.\n\nInformasi pembagian gelombang (*waves*), kuota regu, dan unduh dokumen juklak lengkap dapat diakses langsung pada menu **Lomba PMR** di website kami! Semangat berjuang menjadi juara kemanusiaan! ✨";
        }

        // 25. Pertanyaan seputar Lomba umum
        if (str_contains($lower, 'lomba') || str_contains($lower, 'sua bhakti') || str_contains($lower, 'kompetisi') || str_contains($lower, 'kejuaraan')) {
            return "Halo Sahabat Kemanusiaan! 🏆\n\nPMR Wira SMAN 1 Ciawi secara berkala menyelenggarakan dan mengikuti ajang kompetisi bergengsi seperti **Sua Bhakti Berkarya**.\n\n**Cabang Lomba yang Dibuka:**\n1. **Pertolongan Pertama (PP):** Penanganan kasus medis, luka terbuka, patah tulang, dan RJP.\n2. **Pasang Tandu Darurat:** Kecepatan, kekuatan ikatan, dan kerapihan evakuasi tandu cepat.\n3. **Traveling / Cerdas Cermat:** Pengujian 7 materi pokok kepalangmerahan dan kepemimpinan.\n4. **Video & Poster Kemanusiaan:** Kampanye sosial kesehatan, donor darah, dan pencegahan bencana.\n\nUntuk petunjuk teknis juklak/juknis, tanggal pelaksanaan, dan registrasi online kontingen, silakan kunjungi menu **Lomba PMR** di navbar website ini ya!";
        }

        // 26. Pertanyaan seputar Donor Darah
        if (str_contains($lower, 'donor') || str_contains($lower, 'darah') || str_contains($lower, 'transfusi') || str_contains($lower, 'stok') || str_contains($lower, 'golongan darah')) {
            return "Humanity! Setetes darah kita adalah kehidupan bagi sesama. 🩸\n\nPMR Wira SMAN 1 Ciawi rutin menyelenggarakan aksi **Donor Darah Sukarela** bekerja sama dengan UTD PMI Kabupaten Bogor.\n\n**Syarat Pendonor Darah:**\n• Usia 17 hingga 60 tahun.\n• Berat badan minimal 45 kg.\n• Tekanan darah normal (Sistole 100-160, Diastole 70-100 mmHg).\n• Kadar Hemoglobin (Hb) minimal 12,5 g/dL.\n• Jarak donor minimal 60-90 hari (2-3 bulan) dari donor sebelumnya.\n• Tidak minum obat keras/antibiotik 3 hari terakhir.\n• Wanita tidak sedang hamil, menyusui, atau menstruasi hari pertama-kedua.\n\n4 Sistem Golongan Darah yang dilayani: A, B, AB, dan O (Rhesus Positif/Negatif).\n\nJadwal donor darah berikutnya dan stok kantong darah dapat dipantau di menu **Donor Darah** pada website ini!";
        }

        // 27. Pertanyaan seputar P3K umum
        if (str_contains($lower, 'p3k') || str_contains($lower, 'pertolongan') || str_contains($lower, 'medis') || str_contains($lower, 'kotak obat')) {
            return "Halo Teman Relawan! 🩹\n\nDalam prinsip **Pertolongan Pertama (P3K)** PMR Wira, langkah awal wajib mengutamakan **3A (Aman Diri, Aman Pasien, Aman Lingkungan)**.\n\n• **Pasien Pingsan:** Bawa ke tempat teduh/berventilasi, baringkan terlentang, longgarkan pakaian, dan tinggikan tungkai kaki 20-30 cm.\n• **Pendarahan:** Lakukan penekanan langsung (*direct pressure*) dengan kassa steril, lalu balut tekan dengan mitela.\n• **Mimisan:** Duduk condong ke depan, tekan cuping hidung 5-10 menit, bernapas via mulut.\n• **Patah Tulang:** Pasang bidai melewati minimal 2 sendi, cek PMS sebelum & sesudah.\n• **Keseleo:** Metode RICE (Rest, Ice, Compression, Elevation).\n• **Luka Bakar:** Guyur air mengalir 10-20 menit, jangan oles odol/mentega!\n\n🚨 **Penting:** Segera hubungi tim medis, ruang UKS, atau fasilitas kesehatan terdekat selagi langkah pertolongan pertama ini dilakukan. (Hotline UKS: 0857-7024-6918)";
        }

        // 28. Pertanyaan seputar Bergabung / Pendaftaran Relawan
        if (str_contains($lower, 'gabung') || str_contains($lower, 'syarat daftar') || str_contains($lower, 'cara daftar') || str_contains($lower, 'rekrutmen') || str_contains($lower, 'anggota baru') || str_contains($lower, 'syarat relawan') || str_contains($lower, 'daftar relawan') || str_contains($lower, 'gabung relawan') || (str_contains($lower, 'daftar') && (str_contains($lower, 'pmr') || str_contains($lower, 'ekskul')))) {
            return "Selamat datang Sahabat Kemanusiaan! Bangga sekali melihat tekadmu untuk mengabdi bersama kami! 🤝\n\n**Syarat Bergabung PMR Wira SMAN 1 Ciawi (Ragana Dwi Pantara):**\n1. Siswa-siswi aktif SMA Negeri 1 Ciawi (Kelas X atau XI).\n2. Memiliki jiwa kerelawanan dan dedikasi sosial.\n3. Bersedia mengikuti Diklatsar Ruang & Lapangan.\n4. Memperoleh persetujuan orang tua/wali.\n\nPendaftaran dibuka setiap awal tahun ajaran baru atau saat demo ekskul. Formulir pra-pendaftaran online dapat diisi di menu **Kontak & Gabung Relawan** pada website kami!";
        }

        // 29. Pertanyaan seputar Profil & UKS
        if (str_contains($lower, 'profil') || str_contains($lower, 'alamat') || str_contains($lower, 'lokasi') || str_contains($lower, 'kontak') || str_contains($lower, 'sekretariat') || str_contains($lower, 'uks') || str_contains($lower, 'markas') || str_contains($lower, 'hotline') || str_contains($lower, 'telepon')) {
            return "Halo Sahabat Relawan! 🏫\n\n• **Organisasi:** PMR Wira Unit SMAN 1 Ciawi (Terakreditasi A).\n• **Alamat:** Ruang UKS/PMR SMAN 1 Ciawi, Jl. Veteran No. 46, Pandansari, Ciawi, Bogor.\n• **Hotline Siaga UKS:** 0857-7024-6918 (WhatsApp & Panggilan Cepat Langsung).\n• **Jam Layanan:** Senin - Jumat (07.00 - 16.00 WIB).\n• **Email Resmi:** pmrwirasman1c@gmail.com\n\nDi pojok kanan bawah website ini juga tersedia tombol WhatsApp langsung yang menghubungkan Kakak ke petugas medis siaga UKS!";
        }

        // 30. Smart Knowledge Base (RAG) Fallback Search
        $ragSearchResults = $this->knowledgeSearch->search($message, limit: 1);
        if (! empty($ragSearchResults)) {
            $topResult = $ragSearchResults[0];
            if (($topResult['score'] ?? 0) >= 15 && ! empty($topResult['content'])) {
                $topicTitle = $topResult['title'] ?? 'Informasi PMR Wira';

                return "Halo Sahabat Kemanusiaan! 🌟\n\nBerdasarkan materi resmi PMR Wira SMAN 1 Ciawi mengenai **{$topicTitle}**:\n\n".$topResult['content'];
            }
        }

        // 31. Pertanyaan Terbuka / Luar Materi (Open-Domain Fallback)
        return "Halo Sahabat Kemanusiaan! Salam hangat dari Wira Agent. 🌟\n\nSaya asisten virtual resmi PMR Wira SMAN 1 Ciawi (Masa Bakti Ragana Dwi Pantara 2026/2027).\n\nUntuk pertanyaan umum di luar kurikulum kepalangmerahan dan konten website, Wira Agent dapat memanfaatkan kecerdasan Google Gemini AI apabila variabel `GEMINI_API_KEY` telah disetel pada berkas `.env` server.\n\nSaat ini, Kakak dapat menanyakan informasi resmi kami, antara lain:\n• 👑 **Pimpinan & Struktur Organisasi** (Ketua Umum, Pembina, Wakil, BPH, 5 Bidang, dan seluruh staf anggota)\n• 🩹 **Pertolongan Pertama (P3K)** (Kejang-kejang, Tersedak, Asma, Gigitan Ular, RJP 30:2, Patah Tulang, Keseleo, Luka Bakar, Pingsan)\n• 🪵 **Tandu Darurat** (Ukuran bambu 225 cm, tali 13-14 m, simpul jangkar & palang)\n• 📜 **Materi Kepalangmerahan & PMI** (Henry Dunant, 7 Prinsip PMI, Tri Bakti, Mars PMI)\n• 🏆 **Syarat & Cabang Lomba PMR** (Sua Bhakti Berkarya)\n• 🩸 **Donor Darah Sukarela & Syarat Pendonor**\n• 🚨 **Ayo Siaga Bencana (ASB)** & Mitigasi Gempa/Kebakaran\n• 🏥 **Layanan Kontak & Hotline UKS** (0857-7024-6918)\n\nSilakan tanyakan materi di atas atau sebutkan nama pimpinan/anggota yang ingin Kakak ketahui!";
    }

    /**
     * Dapatkan informasi dan susunan anggota bidang secara real-time dari OrganizationMember & Member.
     * Tidak pernah menggunakan nama aktor palsu/dummy seeder.
     *
     * @param  array{label: string, fullLabel: string, icon: string, keywords: array<int, string>, defaultProgram: string}  $bidang
     * @return array{org_member: ?OrganizationMember, ketua_member: ?Member, ketua_name: ?string, ketua_subtitle: ?string, work_program: string, staff_list: array<int, array{name: string, class_grade: string, nri: string, motto: string}>}
     */
    public function getRealBidangInfo(array $bidang): array
    {
        $ketuaMember = null;
        $ketuaName = null;
        $ketuaSubtitle = null;
        $workProgram = $bidang['defaultProgram'];
        $staffList = [];
        $orgMember = null;

        // 1. Cek dari tabel organization_members (Bagan Kepengurusan Level 5)
        if (Schema::hasTable('organization_members')) {
            try {
                $candidates = OrganizationMember::with('member')
                    ->where(function ($q) {
                        $q->where('level', 5)
                            ->orWhere('position', 'like', '%markas%')
                            ->orWhere('position', 'like', '%pelayanan%')
                            ->orWhere('position', 'like', '%diklat%')
                            ->orWhere('position', 'like', '%humas%')
                            ->orWhere('position', 'like', '%kreasi%');
                    })
                    ->where(function ($q) {
                        $q->where('is_active', true)->orWhereNull('is_active');
                    })
                    ->get();

                // Prioritaskan baris yang memiliki pejabat terisi (member_id atau nama yang valid)
                $sorted = $candidates->sortByDesc(function ($c) {
                    if (! empty($c->member_id)) {
                        return 3;
                    }
                    if (! empty($c->name) && $c->name !== '-') {
                        return 2;
                    }

                    return 0;
                });

                foreach ($sorted as $cand) {
                    $pos = strtolower(($cand->position ?? '').' '.($cand->subtitle ?? '').' '.($cand->name ?? ''));
                    foreach ($bidang['keywords'] as $kw) {
                        if (str_contains($pos, $kw)) {
                            $orgMember = $cand;
                            break 2;
                        }
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        if ($orgMember) {
            if (! empty($orgMember->work_program)) {
                $workProgram = $orgMember->work_program;
            }

            // Jika organization_member berelasi langsung dengan Member
            if ($orgMember->member) {
                $ketuaMember = $orgMember->member;
                $ketuaName = $ketuaMember->name;
            } elseif (! empty($orgMember->name) && $orgMember->name !== '-') {
                if (Schema::hasTable('members')) {
                    try {
                        $ketuaMember = Member::where('name', $orgMember->name)->first();
                        if (! $ketuaMember) {
                            $ketuaMember = Member::where('name', 'like', '%'.$orgMember->name.'%')
                                ->orWhere('name', 'like', '%'.explode(' ', trim($orgMember->name))[0].'%')
                                ->first();
                        }
                    } catch (\Throwable $e) {
                    }
                }
                $ketuaName = $orgMember->name;
            }
            $ketuaSubtitle = $orgMember->subtitle;

            // Ambil staf dari json staff_members
            if (! empty($orgMember->staff_members) && is_array($orgMember->staff_members)) {
                foreach ($orgMember->staff_members as $st) {
                    $stName = is_array($st) ? ($st['name'] ?? null) : null;
                    if ($stName) {
                        $stMember = null;
                        if (Schema::hasTable('members')) {
                            try {
                                $stMember = Member::where('name', $stName)->first();
                            } catch (\Throwable $e) {
                            }
                        }
                        $staffList[] = [
                            'name' => $stName,
                            'class_grade' => $stMember?->class_grade ?? ($st['class_grade'] ?? '-'),
                            'nri' => $stMember?->nri ?? ($st['nri'] ?? '-'),
                            'motto' => $stMember?->motto ?? 'Siap mengabdi untuk kemanusiaan.',
                        ];
                    }
                }
            }
        }

        // 2. Jika ketua belum ditemukan dari organization_members, cari di tabel members
        if (! $ketuaMember && Schema::hasTable('members')) {
            try {
                foreach ($bidang['keywords'] as $kw) {
                    $m = Member::where(function ($q) use ($kw) {
                        $q->where('position', 'like', '%ketua%'.$kw.'%')
                            ->orWhere('position', 'like', '%kabid%'.$kw.'%')
                            ->orWhere('position', 'like', '%koordinator%'.$kw.'%')
                            ->orWhere('position', 'like', '%kepala bidang%'.$kw.'%');
                    })->where('is_active', true)->first();

                    if ($m) {
                        $ketuaMember = $m;
                        $ketuaName = $m->name;
                        break;
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        // 3. Jika staf dari json kosong, cari staf dari tabel members
        if (empty($staffList) && Schema::hasTable('members')) {
            try {
                foreach ($bidang['keywords'] as $kw) {
                    $stMembers = Member::where(function ($q) use ($kw) {
                        $q->where('position', 'like', '%staf%'.$kw.'%')
                            ->orWhere('position', 'like', '%staff%'.$kw.'%')
                            ->orWhere('position', 'like', '%anggota%'.$kw.'%');
                    })->where('is_active', true)->get();

                    if ($stMembers->isNotEmpty()) {
                        foreach ($stMembers as $sm) {
                            $staffList[] = [
                                'name' => $sm->name,
                                'class_grade' => $sm->class_grade ?? '-',
                                'nri' => $sm->nri ?? '-',
                                'motto' => $sm->motto ?: 'Siap mengabdi untuk kemanusiaan.',
                            ];
                        }
                        break;
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        return [
            'org_member' => $orgMember,
            'ketua_member' => $ketuaMember,
            'ketua_name' => $ketuaName,
            'ketua_subtitle' => $ketuaSubtitle,
            'work_program' => $workProgram,
            'staff_list' => $staffList,
        ];
    }

    /**
     * Menangani pertanyaan program kerja bidang (baik seluruh bidang maupun bidang spesifik).
     */
    public function handleBidangWorkProgramInquiry(string $lower): ?string
    {
        $hasWorkProgramKeyword = str_contains($lower, 'program kerja') ||
            str_contains($lower, 'proker') ||
            str_contains($lower, 'tugas pokok') ||
            str_contains($lower, 'rencana kerja') ||
            str_contains($lower, 'agenda kerja') ||
            str_contains($lower, 'program kegiatan') ||
            (str_contains($lower, 'program') && (str_contains($lower, 'bidang') || str_contains($lower, 'kerja'))) ||
            (str_contains($lower, 'kerja') && str_contains($lower, 'bidang')) ||
            (str_contains($lower, 'tugas') && str_contains($lower, 'bidang'));

        if (! $hasWorkProgramKeyword) {
            return null;
        }

        // Cek apakah menanyakan bidang spesifik tertentu
        $matchedBidang = null;
        $matchCount = 0;
        foreach (self::BIDANG_DEFINITIONS as $key => $bidang) {
            foreach ($bidang['keywords'] as $kw) {
                if (str_contains($lower, $kw)) {
                    $matchedBidang = $bidang;
                    $matchCount++;
                    break;
                }
            }
        }

        $explicitAllBidang = str_contains($lower, 'masing-masing') ||
            str_contains($lower, 'masing masing') ||
            str_contains($lower, 'tiap bidang') ||
            str_contains($lower, 'setiap bidang') ||
            str_contains($lower, 'semua bidang') ||
            str_contains($lower, 'seluruh bidang') ||
            str_contains($lower, '5 bidang') ||
            str_contains($lower, 'lima bidang') ||
            str_contains($lower, 'daftar bidang');

        // Jika ada bidang spesifik yang cocok dan pengguna tidak secara eksplisit meminta seluruh bidang
        if ($matchedBidang !== null && $matchCount === 1 && ! $explicitAllBidang) {
            return $this->renderSpecificBidangWorkProgram($matchedBidang);
        }

        // Jika tidak ada bidang spesifik, atau meminta seluruh/masing-masing bidang, atau cocok lebih dari 1 bidang
        return $this->renderAllBidangWorkPrograms();
    }

    /**
     * Render daftar program kerja lengkap seluruh 5 bidang PMR Wira SMAN 1 Ciawi.
     */
    public function renderAllBidangWorkPrograms(): string
    {
        $reply = "Halo Sahabat Kemanusiaan! 🏛️✨\n\n"
            ."Seluruh bidang dalam kepengurusan **PMR Wira SMAN 1 Ciawi (Masa Bakti Ragana Dwi Pantara 2026/2027)** memiliki program kerja strategis yang terarah untuk mengabdi pada kemanusiaan sesuai Tri Bakti PMR dan 7 Prinsip Dasar Palang Merah.\n\n"
            ."Berikut rincian **Program Kerja untuk 5 Bidang Utama**:\n\n";

        $counter = 1;
        foreach (self::BIDANG_DEFINITIONS as $key => $bidang) {
            $info = $this->getRealBidangInfo($bidang);
            $icon = $bidang['icon'];
            $fullLabel = $bidang['fullLabel'];
            $ketuaStr = $info['ketua_name'] ? "**{$info['ketua_name']}**" : '*Belum ditetapkan pada sistem*';
            $proker = $info['work_program'];

            $reply .= "### {$counter}. {$icon} Bidang {$fullLabel}\n"
                ."• **Ketua Bidang:** {$ketuaStr}\n"
                ."• **Program Kerja & Tugas Pokok:**\n"
                ."{$proker}\n\n";

            $counter++;
        }

        $reply .= "📌 *Seluruh program kerja di atas dijalankan secara dinamis dan dapat dipantau perkembangannya pada menu **Tentang Kami** dan **Kegiatan** di website resmi PMR Wira SMAN 1 Ciawi.*\n\n"
            .'💡 *Kakak juga dapat menanyakan program kerja bidang tertentu secara spesifik (contoh: "Program kerja Bidang Markas" atau "Proker Bidang Diklat")!*';

        return $reply;
    }

    /**
     * Render program kerja spesifik untuk satu bidang dengan data live dari database.
     *
     * @param  array{label: string, fullLabel: string, icon: string, keywords: array<int, string>, defaultProgram: string}  $bidang
     */
    public function renderSpecificBidangWorkProgram(array $bidang): string
    {
        $info = $this->getRealBidangInfo($bidang);
        $icon = $bidang['icon'];
        $fullLabel = $bidang['fullLabel'];
        $ketuaName = $info['ketua_name'];
        $ketuaMember = $info['ketua_member'];
        $workProgram = $info['work_program'];

        $reply = "Halo Sahabat Kemanusiaan! {$icon}✨\n\n"
            ."Berikut rincian resmi **Program Kerja & Tugas Pokok Bidang {$fullLabel}** PMR Wira SMAN 1 Ciawi (Masa Bakti Ragana Dwi Pantara 2026/2027):\n\n";

        if ($ketuaName) {
            $ketuaKelas = $ketuaMember?->class_grade ? 'Kelas '.$ketuaMember->class_grade : 'Kelas XI';
            $ketuaNri = $ketuaMember?->nri ? 'NRI: '.$ketuaMember->nri : '-';
            $ketuaMotto = $ketuaMember?->motto ?: 'Siap sedia mengabdi untuk kemanusiaan.';

            $reply .= "👑 **Ketua / Koordinator Bidang:**\n"
                ."• **{$ketuaName}** ({$ketuaKelas} • {$ketuaNri})\n"
                ."  *Motto:* \"{$ketuaMotto}\"\n\n";
        } else {
            $reply .= "👑 **Ketua / Koordinator Bidang:** *Belum ada pengurus yang ditetapkan pada sistem.*\n\n";
        }

        $reply .= "📋 **Daftar Program Kerja & Agenda Unggulan:**\n"
            ."{$workProgram}\n\n";

        if (! empty($info['staff_list'])) {
            $totalStaf = count($info['staff_list']);
            $staffNames = collect($info['staff_list'])->pluck('name')->implode(', ');
            $reply .= "👥 **Didukung oleh {$totalStaf} Staf Relawan:**\n"
                ."{$staffNames}\n\n";
        }

        $reply .= '💡 *Program kerja ini dirancang untuk mewujudkan relawan yang sigap, tangguh, dan berjiwa kemanusiaan sesuai Tri Bakti PMR dan 7 Prinsip Dasar Palang Merah.*';

        return $reply;
    }

    /**
     * Deteksi pertanyaan mengenai bidang tertentu dan jawab dengan data LIVE dari database (OrganizationMember & Member).
     * Jika jabatan belum diisi di database, sampaikan bahwa jabatan belum ditetapkan pada sistem.
     */
    public function handleSpecificBidangInquiry(string $lower): ?string
    {
        $isAskingKetua = str_contains($lower, 'ketua') || str_contains($lower, 'pimpinan') ||
            str_contains($lower, 'kabid') || str_contains($lower, 'kepala') || str_contains($lower, 'koordinator') ||
            str_contains($lower, 'ktua') || str_contains($lower, 'memimpin') || str_contains($lower, 'penanggung jawab') ||
            str_contains($lower, 'pj') || str_contains($lower, 'sispa') || str_contains($lower, 'siapa') || str_contains($lower, 'siapakah');
        $isAskingAnggota = str_contains($lower, 'anggota') || str_contains($lower, 'staf') ||
            str_contains($lower, 'staff') || str_contains($lower, 'tim') ||
            str_contains($lower, 'siapa saja') || str_contains($lower, 'daftar');

        foreach (self::BIDANG_DEFINITIONS as $bidang) {
            $matched = false;
            foreach ($bidang['keywords'] as $kw) {
                if (str_contains($lower, $kw)) {
                    $matched = true;
                    break;
                }
            }

            if (! $matched) {
                continue;
            }

            $info = $this->getRealBidangInfo($bidang);
            $icon = $bidang['icon'];
            $fullLabel = $bidang['fullLabel'];
            $ketuaName = $info['ketua_name'];
            $ketuaMember = $info['ketua_member'];

            // 1. Jika bertanya KETUA secara spesifik
            if ($isAskingKetua && ! $isAskingAnggota) {
                if ($ketuaName) {
                    $ketuaKelas = $ketuaMember?->class_grade ? 'Kelas '.$ketuaMember->class_grade : 'Kelas XI';
                    $ketuaNri = $ketuaMember?->nri ? 'NRI: '.$ketuaMember->nri : '-';
                    $ketuaMotto = $ketuaMember?->motto ?: 'Siap sedia mengabdi untuk kemanusiaan.';

                    return $this->getBiodataCard(
                        name: $ketuaName,
                        position: 'Ketua Bidang '.$bidang['label'].' PMR Wira SMAN 1 Ciawi',
                        levelOrClass: $ketuaKelas,
                        idNumber: $ketuaNri,
                        roleDescription: $info['work_program'] ?: ('Memimpin dan mengkoordinasikan program kerja Bidang '.$bidang['label'].' PMR Wira SMAN 1 Ciawi.'),
                        motto: $ketuaMotto,
                        title: 'BIODATA KETUA BIDANG '.strtoupper($bidang['label'])
                    );
                }

                return "Halo Sahabat Kemanusiaan! 🏛️\n\n"
                    ."Berdasarkan data resmi Bagan Kepengurusan dan Data Anggota PMR Wira SMAN 1 Ciawi saat ini, posisi **Ketua Bidang {$bidang['label']}** belum diisi atau belum ada pengurus yang terdaftar pada sistem.\n\n"
                    .'📌 Pengurus atau pembina dapat memperbarui data kepengurusan melalui menu **Bagan Kepengurusan** di panel backoffice admin.';
            }

            // 2. Pertanyaan umum atau susunan staf bidang
            $reply = "Halo Teman Relawan! {$icon}\n\n"
                ."Berikut susunan kepengurusan **Bidang {$fullLabel}** PMR Wira SMAN 1 Ciawi (Masa Bakti Ragana Dwi Pantara 2026/2027):\n\n";

            if ($ketuaName) {
                $ketuaKelas = $ketuaMember?->class_grade ? 'Kelas '.$ketuaMember->class_grade : 'Kelas XI';
                $ketuaNri = $ketuaMember?->nri ? 'NRI: '.$ketuaMember->nri : '-';
                $ketuaMotto = $ketuaMember?->motto ?: 'Siap sedia mengabdi untuk kemanusiaan.';

                $reply .= "👑 **Ketua Bidang {$bidang['label']}:**\n"
                    ."• **{$ketuaName}** ({$ketuaKelas} • {$ketuaNri})\n"
                    ."  *Motto:* \"{$ketuaMotto}\"\n\n";
            } else {
                $reply .= "👑 **Ketua Bidang {$bidang['label']}:** *Belum ada pengurus yang terdaftar di sistem.*\n\n";
            }

            if (! empty($info['staff_list'])) {
                $reply .= "👥 **Staf Anggota Bidang {$bidang['label']}:**\n";
                foreach ($info['staff_list'] as $i => $s) {
                    $num = $i + 1;
                    $sNri = $s['nri'] !== '-' ? 'NRI: '.$s['nri'] : '-';
                    $sKelas = $s['class_grade'] !== '-' ? 'Kelas '.$s['class_grade'] : '-';
                    $reply .= "{$num}. **{$s['name']}** ({$sKelas} • {$sNri})\n"
                        ."   *Motto:* \"{$s['motto']}\"\n";
                }
                $reply .= "\n✨ *Kakak dapat menanyakan nama anggota di atas (misal: \"Biodata {$info['staff_list'][0]['name']}\") untuk melihat KTA digital resminya!*";
            } else {
                $reply .= "👥 **Staf Anggota Bidang {$bidang['label']}:** *Belum ada data staf yang terdaftar di sistem.*\n\n"
                    ."📋 **Program Kerja & Tugas Pokok:**\n{$info['work_program']}\n\n"
                    .'📌 *Bagan kepengurusan lengkap dapat dilihat di menu **Tentang Kami** pada website.*';
            }

            return $reply;
        }

        return null;
    }

    /**
     * Cari biodata pimpinan atau anggota tertentu dari Pembina sampai seluruh staf 5 bidang.
     * Menggunakan pencarian berbasis bobot/skor kecocokan tertinggi agar setiap nama unik
     * dan tidak tertukar satu sama lain (misal: "Ade Putri Berlian", "Delia Putri", "Dhiora Hayfa Kalyca").
     */
    public function findMemberBiodataByName(string $lower): ?string
    {
        // 1. Cek database tabel members terlebih dahulu (Prioritas Utama: Data Anggota Riil)
        if (Schema::hasTable('members')) {
            try {
                $allMembers = Member::where('is_active', true)->get();
                if ($allMembers->isNotEmpty()) {
                    $bestMember = null;
                    $bestScore = 0;

                    foreach ($allMembers as $member) {
                        $nameLower = strtolower(trim($member->name ?? ''));
                        if (! $nameLower) {
                            continue;
                        }

                        $score = $this->calculateNameMatchScore($lower, $nameLower);
                        if ($score > $bestScore) {
                            $bestScore = $score;
                            $bestMember = $member;
                        }
                    }

                    // Ambang batas kecocokan: minimal skor 50
                    if ($bestMember && $bestScore >= 50) {
                        return $this->formatMemberInquiryResponse($lower, $bestMember);
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        // 2. Cek database tabel organization_members (Pejabat & Staf Anggota)
        if (Schema::hasTable('organization_members')) {
            try {
                $orgMembers = OrganizationMember::with('member')->get();
                $bestOrg = null;
                $bestOrgScore = 0;
                $bestStaff = null;
                $bestStaffParent = null;

                foreach ($orgMembers as $om) {
                    $omName = strtolower(trim($om->name ?? ($om->member?->name ?? '')));
                    if ($omName) {
                        $score = $this->calculateNameMatchScore($lower, $omName);
                        if ($score > $bestOrgScore) {
                            $bestOrgScore = $score;
                            $bestOrg = $om;
                            $bestStaff = null;
                        }
                    }

                    // Cek staf di staff_members
                    if (! empty($om->staff_members) && is_array($om->staff_members)) {
                        foreach ($om->staff_members as $st) {
                            $stName = strtolower(trim(is_array($st) ? ($st['name'] ?? '') : ''));
                            if ($stName) {
                                $score = $this->calculateNameMatchScore($lower, $stName);
                                if ($score > $bestOrgScore) {
                                    $bestOrgScore = $score;
                                    $bestStaff = $st;
                                    $bestStaffParent = $om;
                                    $bestOrg = null;
                                }
                            }
                        }
                    }
                }

                if ($bestOrg && $bestOrgScore >= 50) {
                    if ($bestOrg->member) {
                        return $this->formatMemberInquiryResponse($lower, $bestOrg->member);
                    }

                    return $this->getBiodataCard(
                        name: $bestOrg->name,
                        position: $bestOrg->position ?: 'Pengurus PMR Wira SMAN 1 Ciawi',
                        levelOrClass: $bestOrg->subtitle ?: 'Kelas XI',
                        idNumber: '-',
                        roleDescription: $bestOrg->work_program ?: ('Menjalankan amanah sebagai '.$bestOrg->position.' PMR Wira SMAN 1 Ciawi.'),
                        motto: 'Siap sedia mengabdi untuk kemanusiaan.',
                        title: 'BIODATA PENGURUS PMR WIRA'
                    );
                }

                if ($bestStaff && $bestOrgScore >= 50 && $bestStaffParent) {
                    $stName = is_array($bestStaff) ? ($bestStaff['name'] ?? '') : '';
                    $sNri = ! empty($bestStaff['nri']) ? 'NRI: '.$bestStaff['nri'] : '-';
                    $sKelas = ! empty($bestStaff['class_grade']) ? 'Kelas '.$bestStaff['class_grade'] : 'Kelas X/XI';
                    $sMotto = $bestStaff['motto'] ?? 'Siap sedia mengabdi untuk kemanusiaan.';

                    return $this->getBiodataCard(
                        name: ucwords($stName),
                        position: 'Staf '.$bestStaffParent->position,
                        levelOrClass: $sKelas,
                        idNumber: $sNri,
                        roleDescription: 'Staf operasional bidang '.$bestStaffParent->position.' PMR Wira SMAN 1 Ciawi.',
                        motto: $sMotto,
                        title: 'BIODATA STAF BIDANG '.strtoupper($bestStaffParent->position)
                    );
                }
            } catch (\Throwable $e) {
            }
        }

        // 3. Fallback ke data anggota resmi jika DB kosong / testing
        return $this->findStaticMemberBiodata($lower);
    }

    /**
     * Format respon cerdas berdasarkan jenis pertanyaan (jabatan, kelas, kontak, atau biodata umum).
     */
    protected function formatMemberInquiryResponse(string $lower, Member $member): string
    {
        $card = $this->buildBiodataCardFromMember($member);
        $position = $member->position ?: 'Anggota PMR Wira';
        $kelas = $member->class_grade ? 'Kelas '.$member->class_grade : 'Kelas X/XI';

        // A. Jika ditanya jabatan secara spesifik ("Delia Putri jabatannya apa", "Dhiora jabatannya apa")
        if (
            str_contains($lower, 'jabatan') ||
            str_contains($lower, 'posisi') ||
            str_contains($lower, 'tugas') ||
            str_contains($lower, 'sebagai apa') ||
            str_contains($lower, 'menjabat')
        ) {
            return "Halo Sahabat Kemanusiaan! 🏛️\n\n"
                ."Berdasarkan data resmi PMR Wira SMAN 1 Ciawi, **{$member->name}** memegang jabatan resmi sebagai **{$position}**.\n\n"
                ."Berikut kartu profil dan biodata lengkapnya:\n\n"
                .$card;
        }

        // B. Jika ditanya kelas / angkatan
        if (str_contains($lower, 'kelas') || str_contains($lower, 'tingkat') || str_contains($lower, 'angkatan')) {
            return "Halo Sahabat Kemanusiaan! 🏛️\n\n"
                ."Berdasarkan data anggota PMR Wira SMAN 1 Ciawi, **{$member->name}** saat ini duduk di **{$kelas}** ({$position}).\n\n"
                ."Berikut kartu profil dan biodata lengkapnya:\n\n"
                .$card;
        }

        // C. Jika ditanya kontak / nomor telepon / wa
        if (
            str_contains($lower, 'kontak') ||
            str_contains($lower, 'telepon') ||
            str_contains($lower, 'hp') ||
            str_contains($lower, 'wa') ||
            str_contains($lower, 'whatsapp') ||
            str_contains($lower, 'nomor') ||
            str_contains($lower, 'email')
        ) {
            $kontak = $member->phone ?: ($member->email ?: 'Belum terdaftar di kontak publik');

            return "Halo Sahabat Kemanusiaan! 🏛️\n\n"
                ."Kontak resmi untuk **{$member->name}** ({$position}) adalah **{$kontak}**.\n\n"
                ."Berikut kartu profil dan biodata lengkapnya:\n\n"
                .$card;
        }

        // D. Jika ditanya domisili / alamat
        if (str_contains($lower, 'alamat') || str_contains($lower, 'domisili') || str_contains($lower, 'tinggal') || str_contains($lower, 'rumah')) {
            $alamat = $member->address ?: 'Bogor';

            return "Halo Sahabat Kemanusiaan! 🏛️\n\n"
                ."Alamat/domisili resmi **{$member->name}** berada di **{$alamat}**.\n\n"
                ."Berikut kartu profil dan biodata lengkapnya:\n\n"
                .$card;
        }

        // E. Pertanyaan umum ("siapakah ...", "biodata ...", dll.)
        return "Halo Sahabat Kemanusiaan! 🏛️\n\n"
            ."Berikut data profil dan kartu biodata resmi relawan atas nama **{$member->name}** yang terdaftar di database PMR Wira SMAN 1 Ciawi:\n\n"
            .$card;
    }

    /**
     * Hitung skor kecocokan antara teks pertanyaan pengguna dan nama anggota.
     * Menghasilkan skor tinggi untuk kecocokan nama lengkap (persis) dan membedakan nama yang mirip
     * seperti "Ade Putri Berlian", "Delia Putri", dan "Putri Raisya Anjani".
     */
    public function calculateNameMatchScore(string $query, string $nameLower): int
    {
        // 1. Kecocokan nama lengkap secara persis di dalam query (misal: "ade putri berlian", "delia putri", "dhiora hayfa kalyca")
        if (str_contains($query, $nameLower)) {
            return 1000 + (strlen($nameLower) * 10);
        }

        $parts = preg_split('/\s+/', $nameLower);
        $totalParts = count($parts);
        if ($totalParts === 0) {
            return 0;
        }

        // 2. Cek kecocokan frasa 2 kata berurutan (misal: "ade putri", "putri berlian", "delia putri", "dhiora hayfa")
        $matchedConsecutive = 0;
        if ($totalParts >= 2) {
            for ($i = 0; $i < $totalParts - 1; $i++) {
                $pair = $parts[$i].' '.$parts[$i + 1];
                if (strlen($pair) >= 5 && str_contains($query, $pair)) {
                    $matchedConsecutive++;
                }
            }
        }

        if ($matchedConsecutive > 0) {
            return 500 + ($matchedConsecutive * 150) + (strlen($nameLower) * 5);
        }

        // 3. Hitung berapa banyak kata dari nama yang muncul di query sebagai kata utuh (\bword\b)
        $matchedWords = 0;
        $commonWords = ['muhammad', 'moch', 'mohamad', 'nur', 'siti', 'putri', 'putra', 'dewi', 'ahmad', 'fajar'];

        foreach ($parts as $p) {
            if (strlen($p) < 3) {
                continue;
            }
            if (preg_match('/\b'.preg_quote($p, '/').'\b/i', $query)) {
                $weight = in_array($p, $commonWords, true) ? 1 : 3;
                $matchedWords += $weight;
            }
        }

        // Jika sebagian besar kata cocok
        if ($matchedWords >= ($totalParts * 2)) {
            return 400 + ($matchedWords * 50);
        }

        if ($matchedWords >= 3) {
            return 200 + ($matchedWords * 30);
        }

        // Jika hanya 1 kata cocok dan kata tersebut bukan kata pasaran, serta panjang >= 4
        if ($matchedWords >= 1 && $totalParts >= 1) {
            $first = $parts[0];
            if (strlen($first) >= 4 && ! in_array($first, $commonWords, true) && preg_match('/\b'.preg_quote($first, '/').'\b/i', $query)) {
                return 80 + strlen($first);
            }
        }

        return 0;
    }

    /**
     * Helper pencocokan nama anggota.
     */
    protected function isNameMatch(string $query, string $name): bool
    {
        return $this->calculateNameMatchScore($query, strtolower(trim($name))) >= 50;
    }

    /**
     * Fallback data biodata anggota resmi saat DB belum terisi.
     */
    public function findStaticMemberBiodata(string $lower): ?string
    {
        $staticMembers = [
            // Pimpinan
            'sulthan' => [
                'name' => 'Sulthan Arkana Putra R.', 'pos' => 'Ketua Umum PMR Wira (Kabinet Ragana Dwi Pantara)',
                'class' => 'Kelas XI', 'nri' => 'NRI: 24.49.004 PRT', 'title' => 'BIODATA KETUA UMUM PMR WIRA',
                'desc' => 'Memimpin jalannya organisasi, penanggung jawab umum program kerja kepalangmerahan, koordinasi pimpinan sekolah & PMI, serta mengarahkan 5 bidang operasional relawan.',
                'motto' => 'Siamo Tutti Fratelli — Memimpin dengan keteladanan, melayani sesama dengan ketulusan hati.',
            ],
            'imam abdul' => [
                'name' => 'Imam Abdul Jabbar', 'pos' => 'Wakil Ketua Umum PMR Wira',
                'class' => 'Kelas XI', 'nri' => 'NRI: 24.49.012 PRT', 'title' => 'BIODATA WAKIL KETUA UMUM PMR WIRA',
                'desc' => 'Mendampingi Ketua Umum, mengoordinasikan internal 5 bidang kepengurusan, pengawasan kedisiplinan anggota, serta memimpin rapat evaluasi bulanan.',
                'motto' => 'Berani melangkah, setia mengabdi, dan menjaga soliditas korsa demi kemanusiaan.',
            ],
            'abdul hamid' => [
                'name' => 'Abdul Hamid Saputra, S.Hum., M.Pd.', 'pos' => 'Pembina PMR Wira SMAN 1 Ciawi',
                'class' => 'Dewan Pembina / Guru Pembimbing', 'nri' => 'NIP: 19820415 200801 1 008', 'title' => 'BIODATA PEMBINA PMR WIRA',
                'desc' => 'Membimbing, mengarahkan, dan mengawasi seluruh program kerja kepalangmerahan, pembinaan karakter relawan, serta sinergi antara pihak sekolah dan PMI Cabang.',
                'motto' => 'Mendidik relawan muda yang berkarakter, berakhlak mulia, tangguh, dan tulus mengabdi bagi kemanusiaan.',
            ],
            'putri raisya' => [
                'name' => 'Putri Raisya Anjani', 'pos' => 'Sekretaris Umum (BPH)',
                'class' => 'Kelas XI', 'nri' => 'NRI: 24.49.018 PRT', 'title' => 'BIODATA SEKRETARIS UMUM PMR WIRA',
                'desc' => 'Pengelolaan administrasi resmi, persuratan mandat lomba & dinas, pengarsipan notulensi rapat, pendataan buku induk anggota serta KTA digital relawan.',
                'motto' => 'Tertib administrasi wujud integritas dan dedikasi relawan.',
            ],
            'nisa nurul' => [
                'name' => 'Nisa Nurul Aulia', 'pos' => 'Bendahara Umum (BPH)',
                'class' => 'Kelas XI', 'nri' => 'NRI: 24.49.023 PRT', 'title' => 'BIODATA BENDAHARA UMUM PMR WIRA',
                'desc' => 'Manajemen perbendaharaan kas organisasi, pembukuan keuangan iuran, penganggaran logistik medis UKS, serta pelaporan transparansi dana kegiatan kemanusiaan.',
                'motto' => 'Amanah, transparan, dan akuntabel dalam mengelola dana kemanusiaan.',
            ],
        ];

        foreach ($staticMembers as $key => $s) {
            if (str_contains($lower, $key)) {
                return $this->getBiodataCard(
                    name: $s['name'],
                    position: $s['pos'],
                    levelOrClass: $s['class'],
                    idNumber: $s['nri'],
                    roleDescription: $s['desc'],
                    motto: $s['motto'],
                    title: $s['title']
                );
            }
        }

        return null;
    }

    /**
     * Cari biodata berdasarkan jabatan pimpinan: Pembina, Ketua Umum, Wakil Ketua, Sekretaris, Bendahara.
     * Data diambil LIVE dari DB; jika kosong, pakai data statis sebagai fallback.
     */
    public function findLeaderBiodataByRole(string $lower): ?string
    {
        // Daftar role dengan keyword, posisi DB, title, dan data statis fallback
        $roles = [
            [
                'keywords' => ['wakil ketua', 'wakil'],
                'position' => 'Wakil Ketua',
                'title' => 'BIODATA WAKIL KETUA UMUM PMR WIRA',
                'static' => [
                    'name' => 'Imam Abdul Jabbar', 'position' => 'Wakil Ketua Umum PMR Wira',
                    'kelas' => 'Kelas XI', 'nri' => 'NRI: 24.49.012 PRT',
                    'role' => 'Mendampingi Ketua Umum, mengoordinasikan internal 5 bidang kepengurusan, pengawasan kedisiplinan anggota, serta memimpin rapat evaluasi bulanan.',
                    'motto' => 'Berani melangkah, setia mengabdi, dan menjaga soliditas korsa demi kemanusiaan.',
                ],
            ],
            [
                'keywords' => ['pembina'],
                'position' => 'Pembina PMR',
                'title' => 'BIODATA PEMBINA PMR WIRA',
                'static' => [
                    'name' => 'Abdul Hamid Saputra, S.Hum., M.Pd.', 'position' => 'Pembina PMR Wira SMAN 1 Ciawi',
                    'kelas' => 'Dewan Pembina / Guru Pembimbing', 'nri' => 'NIP: 19820415 200801 1 008',
                    'role' => 'Membimbing, mengarahkan, dan mengawasi seluruh program kerja kepalangmerahan, pembinaan karakter relawan, serta sinergi antara pihak sekolah dan PMI Cabang.',
                    'motto' => 'Mendidik relawan muda yang berkarakter, berakhlak mulia, tangguh, dan tulus mengabdi bagi kemanusiaan.',
                ],
            ],
            [
                'keywords' => ['sekretaris'],
                'position' => 'Sekretaris',
                'title' => 'BIODATA SEKRETARIS UMUM PMR WIRA',
                'static' => [
                    'name' => 'Putri Raisya Anjani', 'position' => 'Sekretaris Umum (BPH)',
                    'kelas' => 'Kelas XI', 'nri' => 'NRI: 24.49.018 PRT',
                    'role' => 'Pengelolaan administrasi resmi, persuratan mandat lomba & dinas, pengarsipan notulensi rapat, pendataan buku induk anggota serta KTA digital relawan.',
                    'motto' => 'Tertib administrasi wujud integritas dan dedikasi relawan.',
                ],
            ],
            [
                'keywords' => ['bendahara'],
                'position' => 'Bendahara',
                'title' => 'BIODATA BENDAHARA UMUM PMR WIRA',
                'static' => [
                    'name' => 'Nisa Nurul Aulia', 'position' => 'Bendahara Umum (BPH)',
                    'kelas' => 'Kelas XI', 'nri' => 'NRI: 24.49.023 PRT',
                    'role' => 'Manajemen perbendaharaan kas organisasi, pembukuan keuangan iuran, penganggaran logistik medis UKS, serta pelaporan transparansi dana kegiatan kemanusiaan.',
                    'motto' => 'Amanah, transparan, dan akuntabel dalam mengelola dana kemanusiaan.',
                ],
            ],
            [
                'keywords' => ['ketua umum', 'ketua pmr'],
                'position' => 'Ketua Umum',
                'title' => 'BIODATA KETUA UMUM PMR WIRA',
                'static' => [
                    'name' => 'Sulthan Arkana Putra R.', 'position' => 'Ketua Umum PMR Wira (Kabinet Ragana Dwi Pantara)',
                    'kelas' => 'Kelas XI', 'nri' => 'NRI: 24.49.004 PRT',
                    'role' => 'Memimpin jalannya organisasi, penanggung jawab umum program kerja kepalangmerahan, koordinasi pimpinan sekolah & PMI, serta mengarahkan 5 bidang operasional relawan.',
                    'motto' => 'Siamo Tutti Fratelli — Memimpin dengan keteladanan, melayani sesama dengan ketulusan hati.',
                ],
            ],
        ];

        foreach ($roles as $role) {
            $matched = false;
            foreach ($role['keywords'] as $kw) {
                if (str_contains($lower, $kw)) {
                    $matched = true;
                    break;
                }
            }

            if (! $matched) {
                continue;
            }

            // Coba query DB live
            if (Schema::hasTable('members')) {
                $member = Member::where('position', 'like', '%'.$role['position'].'%')
                    ->where('is_active', true)
                    ->first();

                if ($member) {
                    return $this->buildBiodataCardFromMember($member, $role['title']);
                }
            }

            // Fallback statis jika DB kosong
            $s = $role['static'];

            return $this->getBiodataCard(
                name: $s['name'],
                position: $s['position'],
                levelOrClass: $s['kelas'],
                idNumber: $s['nri'],
                roleDescription: $s['role'],
                motto: $s['motto'],
                title: $role['title']
            );
        }

        return null;
    }

    /**
     * Bangun kartu biodata dari data Member model (live dari DB).
     */
    public function buildBiodataCardFromMember(Member $member, ?string $title = null): string
    {
        $badge = $title ?: ('BIODATA '.strtoupper($member->position ?? 'ANGGOTA'));
        $officialNri = $this->getFormattedNri($member->name) ?: $member->nri;
        $nri = $officialNri ? (str_starts_with($officialNri, 'NRI') ? $officialNri : 'NRI: '.$officialNri) : ($member->nip ? 'NIP: '.$member->nip : '-');
        $kelas = $member->class_grade ? 'Kelas '.$member->class_grade : 'Dewan Pembina';
        $motto = $member->motto ?: 'Pengabdian tulus untuk kemanusiaan.';
        $position = $member->position ?? '-';

        $roleDesc = '';
        if ($member->vision) {
            $roleDesc .= $member->vision.' ';
        }
        if ($member->mission) {
            $roleDesc .= $member->mission;
        }
        if (! $roleDesc) {
            $roleDesc = $position.' PMR Wira SMAN 1 Ciawi.';
        }

        $status = $member->effective_status ?: 'Aktif';
        $angkatan = $member->entry_year ? 'Tahun Masuk '.$member->entry_year : null;

        $extra = '';
        if ($member->birth_place && $member->birth_date) {
            $tgl = Carbon::parse($member->birth_date)->translatedFormat('d F Y');
            $extra .= "• **Tempat / Tanggal Lahir:** {$member->birth_place}, {$tgl}\n";
        }
        if ($member->gender) {
            $extra .= "• **Jenis Kelamin:** {$member->gender}\n";
        }
        if ($member->address) {
            $extra .= "• **Alamat / Domisili:** {$member->address}\n";
        }
        if ($member->phone) {
            $extra .= "• **Kontak / WhatsApp:** {$member->phone}\n";
        }
        if ($member->email) {
            $extra .= "• **Email Resmi:** {$member->email}\n";
        }

        // Tampilkan info foto profil
        $photoLine = '';
        if (! empty($member->photo)) {
            $photoUrl = $member->photo_url;
            $photoLine = "• **Foto Profil:** [Lihat Foto Profil Resmi]({$photoUrl})\n";
        }

        $unitInfo = $kelas.' • PMR Wira SMAN 1 Ciawi'.($angkatan ? ' ('.$angkatan.')' : '');

        return "🪪 **{$badge}**\n"
            ."━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
            ."• **Nama Lengkap:** {$member->name}\n"
            ."• **Jabatan Resmi:** {$position}\n"
            ."• **Tingkat / Unit:** {$unitInfo}\n"
            ."• **Status / Identitas:** {$nri} (Status: {$status})\n"
            ."• **Masa Bakti:** 2026/2027 (Ragana Dwi Pantara)\n"
            .$extra
            .$photoLine
            ."• **Fungsi & Tugas Pokok:** {$roleDesc}\n"
            ."• **Motto Relawan:** *\"{$motto}\"*\n"
            ."━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
            .'✨ *Kartu profil dan foto resolusi tinggi anggota ini dapat dilihat langsung pada Bagan Struktur Organisasi di halaman Tentang Kami.*';
    }

    /**
     * Dapatkan format NRI resmi anggota PMR Wira.
     */
    public function getFormattedNri(string $name): ?string
    {
        $map = [
            'Sulthan Arkana' => '24.49.004 PRT',
            'Imam Abdul Jabbar' => '24.49.012 PRT',
            'Putri Raisya' => '24.49.018 PRT',
            'Nisa Nurul' => '24.49.023 PRT',
        ];

        $lowerName = strtolower($name);
        foreach ($map as $k => $v) {
            if (str_contains($lowerName, strtolower($k)) || str_contains(strtolower($k), $lowerName)) {
                return $v;
            }
        }

        if (Schema::hasTable('members')) {
            try {
                $m = Member::where('name', 'like', "%{$name}%")->first();
                if ($m && $m->nri) {
                    return $m->nri;
                }
            } catch (\Throwable $e) {
            }
        }

        return null;
    }

    /**
     * @deprecated Digantikan oleh findMemberBiodataByName() dan findLeaderBiodataByRole().
     *             Dipertahankan agar test lama tidak patah.
     */
    public function findSpecificMemberBiodata(string $lower): ?string
    {
        return $this->findMemberBiodataByName($lower) ?? $this->findLeaderBiodataByRole($lower);
    }

    /**
     * Tampilkan Kartu Biodata Digital Resmi dengan format Markdown rapi dan menarik.
     */
    public function getBiodataCard(
        string $name,
        string $position,
        string $levelOrClass,
        string $idNumber,
        string $roleDescription,
        string $motto,
        ?string $title = null
    ): string {
        $badge = $title ? "{$title}" : 'KARTU TANDA ANGGOTA & BIODATA DIGITAL RESMI';

        return "🪪 **{$badge}**\n"
            ."━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
            ."• **Nama Lengkap:** {$name}\n"
            ."• **Jabatan Resmi:** {$position}\n"
            ."• **Tingkat / Unit:** {$levelOrClass} • PMR Wira SMAN 1 Ciawi\n"
            ."• **Status / Identitas:** {$idNumber}\n"
            ."• **Masa Bakti:** 2026/2027 (Ragana Dwi Pantara)\n"
            ."• **Fungsi & Tugas Pokok:** {$roleDescription}\n"
            ."• **Motto Relawan:** *\"{$motto}\"*\n"
            ."━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
            .'✨ *Kartu profil dan foto resolusi tinggi anggota ini dapat dilihat langsung pada Bagan Struktur Organisasi di halaman Tentang Kami.*';
    }

    /**
     * Tampilkan Bagan Struktur Kepengurusan Lengkap dari Pembina sampai seluruh staf anggota.
     */
    /**
     * Tampilkan Bagan Struktur Kepengurusan Lengkap — data LIVE dari DB.
     */
    public function renderFullOrganizationStructure(): string
    {
        $intro = "Halo Sahabat Kemanusiaan! 🏛️\n\n"
            .'Berikut adalah **Struktur Organisasi Resmi PMR Wira SMA Negeri 1 Ciawi** '
            ."(Masa Bakti Ragana Dwi Pantara 2026/2027) dari Pembina sampai seluruh staf anggota:\n\n";

        // Jika DB belum ada / testing, fallback ke data statis
        if (! Schema::hasTable('members')) {
            return $intro.$this->staticOrgStructure();
        }

        $members = Member::where('is_active', true)->orderBy('id')->get();

        if ($members->isEmpty()) {
            return $intro.$this->staticOrgStructure();
        }

        // Kelompokkan berdasarkan jabatan
        $pembina = $members->filter(fn ($m) => str_contains(strtolower($m->position ?? ''), 'pembina'));
        $ketua = $members->filter(fn ($m) => strtolower($m->position ?? '') === 'ketua umum' || str_contains(strtolower($m->position ?? ''), 'ketua umum'));
        $wakil = $members->filter(fn ($m) => str_contains(strtolower($m->position ?? ''), 'wakil'));
        $bph = $members->filter(fn ($m) => in_array(strtolower($m->position ?? ''), ['sekretaris', 'bendahara']) || str_contains(strtolower($m->position ?? ''), 'sekretaris') || str_contains(strtolower($m->position ?? ''), 'bendahara'));
        $bidangList = ['Markas', 'Pelayanan', 'Diklat', 'Humas', 'Kreasi'];
        $bidangIcons = ['Markas' => '📦', 'Pelayanan' => '🩹', 'Diklat' => '📚', 'Humas' => '📢', 'Kreasi' => '🎨'];

        $out = $intro;

        // Level 1: Pembina
        $out .= "👔 **LEVEL 1: DEWAN PEMBINA PMR**\n";
        if ($pembina->isNotEmpty()) {
            foreach ($pembina as $p) {
                $id = $p->nip ? 'NIP: '.$p->nip : ($p->nri ? 'NRI: '.$p->nri : '-');
                $out .= "• **{$p->name}** ({$id})\n";
            }
        }
        $out .= "\n";

        // Level 2: Ketua Umum
        $out .= "👑 **LEVEL 2: PIMPINAN UTAMA**\n";
        foreach ($ketua as $k) {
            $nri = $k->nri ? 'NRI: '.$k->nri : '-';
            $kelas = $k->class_grade ? 'Kelas '.$k->class_grade : '-';
            $out .= "• **Ketua Umum:** {$k->name} ({$kelas} • {$nri})\n";
        }
        $out .= "\n";

        // Level 3: Wakil
        $out .= "🛡️ **LEVEL 3: WAKIL PIMPINAN**\n";
        foreach ($wakil as $w) {
            $nri = $w->nri ? 'NRI: '.$w->nri : '-';
            $kelas = $w->class_grade ? 'Kelas '.$w->class_grade : '-';
            $out .= "• **Wakil Ketua Umum:** {$w->name} ({$kelas} • {$nri})\n";
        }
        $out .= "\n";

        // Level 4: BPH
        $out .= "📋 **LEVEL 4: BADAN PENGURUS HARIAN (BPH)**\n";
        foreach ($bph as $b) {
            $nri = $b->nri ? 'NRI: '.$b->nri : '-';
            $kelas = $b->class_grade ? 'Kelas '.$b->class_grade : '-';
            $out .= "• **{$b->position}:** {$b->name} ({$kelas} • {$nri})\n";
        }
        $out .= "\n";

        // Level 5: 5 Bidang
        $out .= "🏢 **LEVEL 5: 5 BIDANG & SELURUH STAF ANGGOTA**\n\n";
        $num = 1;
        foreach (self::BIDANG_DEFINITIONS as $bid) {
            $info = $this->getRealBidangInfo($bid);
            $icon = $bid['icon'];
            $out .= "{$num}. {$icon} **Bidang {$bid['label']}:**\n";
            if ($info['ketua_name']) {
                $nri = $info['ketua_member']?->nri ? 'NRI: '.$info['ketua_member']->nri : '-';
                $kelas = $info['ketua_member']?->class_grade ? 'Kelas '.$info['ketua_member']->class_grade : '-';
                $out .= "   • **Ketua Bidang:** {$info['ketua_name']} ({$kelas} • {$nri})\n";
            } else {
                $out .= "   • **Ketua Bidang:** *Belum diisi pada sistem*\n";
            }
            if (! empty($info['staff_list'])) {
                $out .= "   • **Staf Anggota:**\n";
                foreach ($info['staff_list'] as $s) {
                    $sNri = $s['nri'] !== '-' ? 'NRI: '.$s['nri'] : '-';
                    $sKelas = $s['class_grade'] !== '-' ? 'Kelas '.$s['class_grade'] : '-';
                    $out .= "     - {$s['name']} ({$sKelas} • {$sNri})\n";
                }
            }
            $out .= "\n";
            $num++;
        }

        $out .= '✨ *Kakak dapat menyebutkan nama pengurus (misal: "Biodata [nama]") untuk melihat kartu biodata digital resminya!*';

        return $out;
    }

    /**
     * Fallback statis struktur organisasi (dipakai saat DB kosong / testing).
     */
    private function staticOrgStructure(): string
    {
        return "👔 **LEVEL 1: DEWAN PEMBINA PMR**\n"
            ."• **Pembina PMR:** Abdul Hamid Saputra, S.Hum., M.Pd.\n\n"
            ."👑 **LEVEL 2: PIMPINAN UTAMA**\n"
            ."• **Ketua Umum:** Sulthan Arkana Putra R. (NRI: 24.49.004 PRT)\n\n"
            ."🛡️ **LEVEL 3: WAKIL PIMPINAN**\n"
            ."• **Wakil Ketua Umum:** Imam Abdul Jabbar (NRI: 24.49.012 PRT)\n\n"
            ."📋 **LEVEL 4: BADAN PENGURUS HARIAN (BPH)**\n"
            ."• **Sekretaris Umum:** Putri Raisya Anjani (NRI: 24.49.018 PRT)\n"
            ."• **Bendahara Umum:** Nisa Nurul Aulia (NRI: 24.49.023 PRT)\n\n"
            ."🏢 **LEVEL 5: 5 BIDANG & SELURUH STAF ANGGOTA**\n"
            ."1. 📦 **Bidang Markas (Logistik UKS)**\n"
            ."2. 🩹 **Bidang Pelayanan (Medis & Donor Darah)**\n"
            ."3. 📚 **Bidang Diklat (Pendidikan & Pelatihan)**\n"
            ."4. 📢 **Bidang Humas (Hubungan Masyarakat)**\n"
            ."5. 🎨 **Bidang Kreasi (Media Kreatif & Publikasi)**\n\n"
            .'✨ *Untuk data lengkap & terkini, lihat halaman Tentang Kami atau hubungi admin untuk memperbarui data anggota di sistem.*';
    }
}
