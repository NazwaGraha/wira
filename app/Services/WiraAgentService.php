<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Article;
use App\Models\BloodStock;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\PopupBanner;
use App\Models\UksHotline;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WiraAgentService
{
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

        // 5. Fallback Cerdas: PMR Wira Dynamic Knowledge Engine (Mengambil data live dari website)
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
        // 1. Ketua Bidang Diklat
        if (str_contains($lower, 'ketua') && str_contains($lower, 'diklat') && ! str_contains($response, 'Reza Rahadian')) {
            return $this->handleSpecificBidangInquiry($lower) ?: $response;
        }

        // 2. Ketua Bidang Markas
        if (str_contains($lower, 'ketua') && str_contains($lower, 'markas') && ! str_contains($response, 'Indra Gunawan')) {
            return $this->handleSpecificBidangInquiry($lower) ?: $response;
        }

        // 3. Ketua Bidang Pelayanan
        if (str_contains($lower, 'ketua') && str_contains($lower, 'pelayanan') && ! str_contains($response, 'Sarah Azhari')) {
            return $this->handleSpecificBidangInquiry($lower) ?: $response;
        }

        // 4. Ketua Bidang Humas
        if (str_contains($lower, 'ketua') && str_contains($lower, 'humas') && ! str_contains($response, 'Dian Sastrowardoyo')) {
            return $this->handleSpecificBidangInquiry($lower) ?: $response;
        }

        // 5. Ketua Bidang Kreasi
        if (str_contains($lower, 'ketua') && str_contains($lower, 'kreasi') && ! str_contains($response, 'Nicholas Saputra')) {
            return $this->handleSpecificBidangInquiry($lower) ?: $response;
        }

        // 6. Anggota Bidang Diklat
        if ((str_contains($lower, 'anggota') || str_contains($lower, 'staf') || str_contains($lower, 'siapa saja')) && str_contains($lower, 'diklat') && ! str_contains($response, 'Fajar Pratama')) {
            return $this->handleSpecificBidangInquiry($lower) ?: $response;
        }

        // 7. Anggota Bidang Markas
        if ((str_contains($lower, 'anggota') || str_contains($lower, 'staf') || str_contains($lower, 'siapa saja')) && str_contains($lower, 'markas') && ! str_contains($response, 'Rian Ardiansyah')) {
            return $this->handleSpecificBidangInquiry($lower) ?: $response;
        }

        // 8. Anggota Bidang Pelayanan
        if ((str_contains($lower, 'anggota') || str_contains($lower, 'staf') || str_contains($lower, 'siapa saja')) && str_contains($lower, 'pelayanan') && ! str_contains($response, 'Anisa Rahma')) {
            return $this->handleSpecificBidangInquiry($lower) ?: $response;
        }

        // 9. Ketua Umum PMR
        if ((str_contains($lower, 'ketua umum') || (str_contains($lower, 'ketua') && (str_contains($lower, 'pmr') || str_contains($lower, 'sekarang')) && ! str_contains($lower, 'bidang') && ! str_contains($lower, 'wakil'))) && ! str_contains($response, 'Sulthan Arkana')) {
            return $this->matchDeterministicInquiry($lower) ?: $response;
        }

        // 10. Wakil Ketua
        if (str_contains($lower, 'wakil') && ! str_contains($response, 'Imam Abdul Jabbar')) {
            return $this->findSpecificMemberBiodata($lower) ?: $response;
        }

        // 11. Pembina PMR
        if (str_contains($lower, 'pembina') && ! str_contains($lower, 'anggota') && ! str_contains($lower, 'sampai') && ! str_contains($response, 'Abdul Hamid')) {
            return $this->findSpecificMemberBiodata($lower) ?: $response;
        }

        return $response;
    }

    /**
     * Cocokkan pertanyaan spesifik kepengurusan, pimpinan, ketua bidang, dan anggota (Prioritas 1).
     */
    public function matchDeterministicInquiry(string $lower): ?string
    {
        // A. Cek jika meminta struktur organisasi lengkap / seluruh susunan
        if (str_contains($lower, 'struktur organisasi') || str_contains($lower, 'bagan organisasi') || str_contains($lower, 'susunan pengurus') || str_contains($lower, 'semua pengurus') || str_contains($lower, 'daftar pengurus') || str_contains($lower, 'siapa saja pengurus') || (str_contains($lower, 'pembina') && (str_contains($lower, 'sampai') || str_contains($lower, 'seluruh') || str_contains($lower, 'semua') || str_contains($lower, 'anggota'))) || str_contains($lower, 'seluruh anggota')) {
            return $this->renderFullOrganizationStructure();
        }

        // B. Cek pertanyaan spesifik mengenai bidang tertentu (Ketua Bidang / Anggota Bidang)
        $bidangInquiry = $this->handleSpecificBidangInquiry($lower);
        if ($bidangInquiry !== null) {
            return $bidangInquiry;
        }

        // C. Cek pencarian biodata spesifik anggota/pimpinan (Pembina, Ketua, Wakil, BPH, Staf)
        $memberBiodata = $this->findSpecificMemberBiodata($lower);
        if ($memberBiodata !== null) {
            return $memberBiodata;
        }

        // D. Cek jika menanyakan Ketua Umum PMR (misal: "siapa ketua pmr", "siapa ketua periode sekarang", "ketua umum")
        if (str_contains($lower, 'ketua umum') || ((str_contains($lower, 'siapa') || str_contains($lower, 'biodata')) && str_contains($lower, 'ketua') && (str_contains($lower, 'pmr') || str_contains($lower, 'sekarang') || str_contains($lower, 'periode')) && ! str_contains($lower, 'bidang'))) {
            return $this->getBiodataCard(
                name: 'Sulthan Arkana Putra R.',
                position: 'Ketua Umum PMR Wira (Kabinet Ragana Dwi Pantara)',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.004 PRT',
                roleDescription: 'Memimpin jalannya organisasi, penanggung jawab umum program kerja kepalangmerahan, koordinasi pimpinan sekolah & PMI, serta mengarahkan 5 bidang operasional relawan.',
                motto: 'Siamo Tutti Fratelli — Memimpin dengan keteladanan, melayani sesama dengan ketulusan hati.',
                title: 'BIODATA KETUA UMUM PMR WIRA'
            );
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
   - Pahami korelasi dua arah: Jika ditanya jabatan atau ketua bidang (misal: "Siapa Ketua Bidang Diklat?"), WAJIB LANGSUNG sebutkan nama pejabat terkait (Reza Rahadian) beserta biodata resminya (NRI, Kelas, Tugas Pokok, Motto). JANGAN PERNAH hanya merangkum 5 bidang secara umum bila ditanya nama ketua atau pengurus bidang tertentu!
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
            $context[] = '  1. Bidang Markas:';
            $context[] = '     - Ketua Bidang: Indra Gunawan (Kelas XI • NRI: 24.49.029 PRT)';
            $context[] = '     - Staf Anggota: Rian Ardiansyah (Kelas X-1 • NRI: 25.50.007 PRT), Siti Fatimah (Kelas X-3 • NRI: 25.50.014 PRT), Deni Prasetyo (Kelas XI-2 • NRI: 24.49.031 PRT)';
            $context[] = '     - Tugas: Pengelolaan inventaris obat UKS, perawatan tandu darurat & spalk bidai, piket kebersihan markas.';
            $context[] = '  2. Bidang Pelayanan:';
            $context[] = '     - Ketua Bidang: Sarah Azhari (Kelas XI • NRI: 24.49.034 PRT)';
            $context[] = '     - Staf Anggota: Anisa Rahma (Kelas X-2 • NRI: 25.50.019 PRT), Budi Santoso (Kelas XI-1 • NRI: 24.49.035 PRT), Dewi Sartika (Kelas X-5 • NRI: 25.50.028 PRT)';
            $context[] = '     - Tugas: Siaga posko medis upacara bendera dan event olahraga, donor darah sukarela bersama UTD PMI Kab. Bogor, posyandu remaja & pembagian TTD.';
            $context[] = '  3. Bidang Diklat (Pendidikan & Pelatihan):';
            $context[] = '     - Ketua Bidang: Reza Rahadian (Kelas XI • NRI: 24.49.040 PRT)';
            $context[] = '     - Staf Anggota: Fajar Pratama (Kelas X-4 • NRI: 25.50.033 PRT), Maya Anggraini (Kelas XI-3 • NRI: 24.49.042 PRT)';
            $context[] = '     - Tugas: Penyusunan kurikulum P3K, Diklatsar ruang dan lapangan, latihan rutin mingguan, pembinaan kontingen lomba.';
            $context[] = '  4. Bidang Humas (Hubungan Masyarakat):';
            $context[] = '     - Ketua Bidang: Dian Sastrowardoyo (Kelas XI • NRI: 24.49.044 PRT)';
            $context[] = '     - Staf Anggota: Gilang Ramadhan (Kelas X-1 • NRI: 25.50.045 PRT), Tiara Andini (Kelas XI-4 • NRI: 24.49.049 PRT), Kevin Julio (Kelas X-3 • NRI: 25.50.052 PRT)';
            $context[] = '     - Tugas: Kemitraan dengan PMI Kabupaten Bogor, sekolah mitra se-Jawa Barat, publikasi media sosial, dan rekrutmen relawan baru.';
            $context[] = '  5. Bidang Kreasi (Media Kreatif):';
            $context[] = '     - Ketua Bidang: Nicholas Saputra (Kelas XI • NRI: 24.49.055 PRT)';
            $context[] = '     - Staf Anggota: Putri Marino (Kelas X-2 • NRI: 25.50.058 PRT), Aldi Taher (Kelas XI-5 • NRI: 24.49.061 PRT)';
            $context[] = '     - Tugas: Produksi poster visual edukasi kesehatan, konten video pertolongan pertama, perancangan merchandise & id card.';
        } catch (\Throwable $e) {
            $context[] = '• Struktur: Pembina Abdul Hamid Saputra, Ketua Sulthan Arkana Putra R., Wakil Imam Abdul Jabbar, Sekretaris Putri Raisya Anjani, Bendahara Nisa Nurul Aulia, beserta 5 Bidang (Markas, Pelayanan, Diklat, Humas, Kreasi).';
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
        if (str_contains($lower, 'bidang') && (str_contains($lower, 'markas') || str_contains($lower, 'pelayanan') || str_contains($lower, 'diklat') || str_contains($lower, 'humas') || str_contains($lower, 'kreasi') || str_contains($lower, '5 bidang') || str_contains($lower, 'struktur'))) {
            return "Halo Teman Relawan! 🏛️\n\nKepengurusan PMR Wira SMAN 1 Ciawi Masa Bakti Ragana Dwi Pantara 2026/2027 ditopang oleh **5 Bidang Utama**:\n\n1. 📦 **Bidang Markas (Ketua: Indra Gunawan):** Inventaris obat UKS, perawatan tandu darurat & spalk bidai, kebersihan markas relawan (Staf: Rian Ardiansyah, Siti Fatimah, Deni Prasetyo).\n2. 🩹 **Bidang Pelayanan (Ketua: Sarah Azhari):** Piket medis harian UKS, tim medis upacara bendera, donor darah bersama UTD PMI Kab. Bogor, posyandu remaja (Staf: Anisa Rahma, Budi Santoso, Dewi Sartika).\n3. 📚 **Bidang Diklat (Ketua: Reza Rahadian):** Silabus kurikulum P3K, Diklatsar ruang dan lapangan, latihan mingguan, pembinaan kontingen lomba (Staf: Fajar Pratama, Maya Anggraini).\n4. 📢 **Bidang Humas (Ketua: Dian Sastrowardoyo):** Kemitraan PMI Kab. Bogor, publikasi media sosial, buletin mading, rekrutmen anggota (Staf: Gilang Ramadhan, Tiara Andini, Kevin Julio).\n5. 🎨 **Bidang Kreasi (Ketua: Nicholas Saputra):** Poster visual edukasi kesehatan, konten video pertolongan pertama, desain ID card & merchandise relawan (Staf: Putri Marino, Aldi Taher).\n\nKakak dapat menanyakan nama pimpinan atau staf mana pun untuk melihat kartu biodata resminya!";
        }

        // 22. Permintaan Biodata Umum
        if (str_contains($lower, 'biodata') || str_contains($lower, 'profil')) {
            return "Halo Sahabat Kemanusiaan! 🪪\n\nUntuk melihat **Biodata Lengkap Relawan PMR Wira SMAN 1 Ciawi**, Kakak dapat menyebutkan nama atau posisi beliau secara langsung, contohnya:\n• *\"Biodata Pembina PMR\"*\n• *\"Biodata Ketua Umum\"* (Sulthan Arkana Putra R.)\n• *\"Biodata Wakil Ketua\"* (Imam Abdul Jabbar)\n• *\"Biodata Sekretaris\"* (Putri Raisya Anjani)\n• *\"Biodata Bendahara\"* (Nisa Nurul Aulia)\n• *\"Biodata Ketua/Staf Bidang Markas, Pelayanan, Diklat, Humas, atau Kreasi\"* (seperti Indra Gunawan, Sarah Azhari, Reza Rahadian, Dian Sastrowardoyo, Nicholas Saputra, Rian Ardiansyah, Anisa Rahma, Gilang Ramadhan, Putri Marino, dll)\n\nSeluruh foto potret resolusi tinggi dan Kartu Tanda Anggota (KTA) digital juga dapat dibuka langsung di menu **Tentang Kami** pada bagan struktur organisasi website!";
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
     * Menangani pertanyaan spesifik mengenai pimpinan maupun seluruh staf anggota pada 5 Bidang Kepengurusan.
     */
    public function handleSpecificBidangInquiry(string $lower): ?string
    {
        // Jika menanyakan nama anggota tertentu secara spesifik, serahkan ke findSpecificMemberBiodata
        $specificIndividualNames = [
            'rian', 'siti fatimah', 'deni',
            'anisa', 'budi santoso', 'dewi sartika',
            'fajar pratama', 'maya',
            'gilang', 'tiara andini', 'kevin julio',
            'putri marino', 'aldi taher',
        ];
        foreach ($specificIndividualNames as $indName) {
            if (str_contains($lower, $indName)) {
                return null;
            }
        }

        $isAskingKetua = str_contains($lower, 'ketua') || str_contains($lower, 'pimpinan') || str_contains($lower, 'kabid') || str_contains($lower, 'kepala') || str_contains($lower, 'koordinator');
        $isAskingAnggota = str_contains($lower, 'anggota') || str_contains($lower, 'staf') || str_contains($lower, 'staff') || str_contains($lower, 'tim') || str_contains($lower, 'siapa saja') || str_contains($lower, 'nama') || str_contains($lower, 'daftar');

        // 1. Bidang Diklat
        if (str_contains($lower, 'diklat') || str_contains($lower, 'pendidikan dan pelatihan') || (str_contains($lower, 'pendidikan') && str_contains($lower, 'pelatihan'))) {
            if ($isAskingKetua && ! $isAskingAnggota) {
                return $this->getBiodataCard(
                    name: 'Reza Rahadian',
                    position: 'Ketua Bidang Pendidikan & Pelatihan (Diklat)',
                    levelOrClass: 'Kelas XI',
                    idNumber: 'NRI: 24.49.040 PRT',
                    roleDescription: 'Penyusunan kurikulum 7 Materi Pokok PMR Wira, penyelenggaraan Diklatsar Ruang & Lapangan, serta pelatihan rutin mingguan.',
                    motto: 'Pendidikan kepalangmerahan yang unggul mencetak relawan yang tangguh dan terampil.',
                    title: 'BIODATA KETUA BIDANG DIKLAT'
                );
            }

            return "Halo Teman Relawan! 📚\n\nBerikut adalah susunan kepengurusan lengkap **Bidang Pendidikan & Pelatihan (Diklat)** PMR Wira SMAN 1 Ciawi Masa Bakti Ragana Dwi Pantara 2026/2027:\n\n👑 **Ketua Bidang Diklat:**\n• **Reza Rahadian** (Kelas XI • NRI: 24.49.040 PRT)\n  *Tugas:* Penyusunan kurikulum 7 Materi Pokok PMR Wira, penyelenggaraan Diklatsar Ruang & Lapangan, serta memimpin pelatihan rutin mingguan.\n  *Motto:* 'Pendidikan kepalangmerahan yang unggul mencetak relawan yang tangguh dan terampil.'\n\n👥 **Staf Anggota Bidang Diklat:**\n1. **Fajar Pratama** (Kelas X-4 • NRI: 25.50.033 PRT)\n   *Tugas:* Instruktur materi pertolongan pertama dasar, pembimbingan simulasi RJP dan balut bidai bagi calon anggota baru.\n   *Motto:* 'Belajar tiada henti demi pertolongan pertama yang tepat dan terstandar.'\n2. **Maya Anggraini** (Kelas XI-3 • NRI: 24.49.042 PRT)\n   *Tugas:* Pendampingan kontingen lomba ketangkasan PMR, modul evaluasi traveling kepalangmerahan, serta absensi latihan rutin.\n   *Motto:* 'Semangat berbagi ilmu dan melatih sesama calon relawan.'\n\n🎯 **Fokus Program Kerja:**\n• Diklatsar Ruang & Lapangan bagi calon relawan baru.\n• Pelatihan rutin mingguan materi P3K, Tandu, dan Mitigasi Bencana.\n• Bimbingan intensif kontingen kejuaraan kepalangmerahan.\n\nKakak dapat menanyakan *'Biodata Reza Rahadian'* atau *'Biodata Fajar Pratama'* untuk melihat KTA digital masing-masing!";
        }

        // 2. Bidang Markas
        if (str_contains($lower, 'markas') || str_contains($lower, 'logistik')) {
            if ($isAskingKetua && ! $isAskingAnggota) {
                return $this->getBiodataCard(
                    name: 'Indra Gunawan',
                    position: 'Ketua Bidang Markas & Logistik UKS',
                    levelOrClass: 'Kelas XI',
                    idNumber: 'NRI: 24.49.029 PRT',
                    roleDescription: 'Koordinator sarana prasarana UKS, pengecekan berkala masa kedaluwarsa obat, pemeliharaan tandu darurat & spalk bidai, serta jadwal piket markas.',
                    motto: 'Markas yang tertata dan siap siaga adalah cerminan respon medis yang tanggap.',
                    title: 'BIODATA KETUA BIDANG MARKAS'
                );
            }

            return "Halo Teman Relawan! 📦\n\nBerikut adalah susunan kepengurusan lengkap **Bidang Markas & Logistik UKS** PMR Wira SMAN 1 Ciawi Masa Bakti Ragana Dwi Pantara 2026/2027:\n\n👑 **Ketua Bidang Markas:**\n• **Indra Gunawan** (Kelas XI • NRI: 24.49.029 PRT)\n  *Tugas:* Koordinator sarana prasarana UKS, pengecekan berkala obat kedaluwarsa, pemeliharaan tandu darurat & spalk bidai, serta jadwal piket markas.\n  *Motto:* 'Markas yang tertata dan siap siaga adalah cerminan respon medis yang tanggap.'\n\n👥 **Staf Anggota Bidang Markas:**\n1. **Rian Ardiansyah** (Kelas X-1 • NRI: 25.50.007 PRT)\n   *Tugas:* Perawatan alat evakuasi tandu bambu 225 cm, penyusunan stok kasa & mitela steril, serta kebersihan ruang posko UKS.\n   *Motto:* 'Siap sedia merawat sarana evakuasi untuk keselamatan bersama.'\n2. **Siti Fatimah** (Kelas X-3 • NRI: 25.50.014 PRT)\n   *Tugas:* Pencatatan inventaris obat masuk dan keluar, pemeriksaan kotak P3K kelas, dan kesiapan tabung oksigen UKS.\n   *Motto:* 'Disiplin mengelola logistik medis demi kelancaran tugas kemanusiaan.'\n3. **Deni Prasetyo** (Kelas XI-2 • NRI: 24.49.031 PRT)\n   *Tugas:* Pemeliharaan perangkat darurat, uji kekuatan simpul tandu, serta logistik simulasi mitigasi bencana sekolah.\n   *Motto:* 'Ketangguhan fisik dan ketelitian merawat perlengkapan medis.'\n\n🎯 **Fokus Program Kerja:**\n• Standarisasi kotak P3K di setiap ruang kelas dan laboratorium.\n• Kalibrasi alat medis tensimeter, termometer digital, dan tabung O2.\n• Perawatan bambu tandu 225 cm dan tali tandu 13-14 m.\n\nKakak dapat menanyakan nama beliau (misal: *'Biodata Indra Gunawan'* atau *'Biodata Siti Fatimah'*) untuk melihat KTA digital resminya!";
        }

        // 3. Bidang Pelayanan
        if (str_contains($lower, 'pelayanan') || str_contains($lower, 'pelayanan medis')) {
            if ($isAskingKetua && ! $isAskingAnggota) {
                return $this->getBiodataCard(
                    name: 'Sarah Azhari',
                    position: 'Ketua Bidang Pelayanan Medis & Donor Darah',
                    levelOrClass: 'Kelas XI',
                    idNumber: 'NRI: 24.49.034 PRT',
                    roleDescription: 'Penanggung jawab posko medis upacara bendera, koordinasi aksi donor darah bersama PMI Kab. Bogor, serta posyandu remaja sekolah.',
                    motto: 'Melayani dengan empati, sigap menolong sesama tanpa pamrih.',
                    title: 'BIODATA KETUA BIDANG PELAYANAN'
                );
            }

            return "Halo Teman Relawan! 🩹\n\nBerikut adalah susunan kepengurusan lengkap **Bidang Pelayanan Medis & Donor Darah** PMR Wira SMAN 1 Ciawi Masa Bakti Ragana Dwi Pantara 2026/2027:\n\n👑 **Ketua Bidang Pelayanan:**\n• **Sarah Azhari** (Kelas XI • NRI: 24.49.034 PRT)\n  *Tugas:* Penanggung jawab posko medis upacara bendera, koordinasi aksi donor darah bersama PMI Kab. Bogor, serta posyandu remaja sekolah.\n  *Motto:* 'Melayani dengan empati, sigap menolong sesama tanpa pamrih.'\n\n👥 **Staf Anggota Bidang Pelayanan:**\n1. **Anisa Rahma** (Kelas X-2 • NRI: 25.50.019 PRT)\n   *Tugas:* Piket medis harian UKS, pemeriksaan tanda vital (tensi darah, suhu tubuh), serta pendampingan siswa sakit di ruang isolasi.\n   *Motto:* 'Tangan terampil, hati tulus melayani mereka yang membutuhkan.'\n2. **Budi Santoso** (Kelas XI-1 • NRI: 24.49.035 PRT)\n   *Tugas:* Tim evakuasi lapangan, pengawalan medis kegiatan olahraga & ekstrakurikuler, serta penanganan kasus cedera.\n   *Motto:* 'Sigap dalam bertindak, tenang dalam pertolongan darurat.'\n3. **Dewi Sartika** (Kelas X-5 • NRI: 25.50.028 PRT)\n   *Tugas:* Edukasi pencegahan anemia remaja putri (pembagian tablet tambah darah), pencatatan rekam medis harian UKS.\n   *Motto:* 'Kemanusiaan adalah panggilan jiwa untuk meringankan penderitaan sesama.'\n\n🎯 **Fokus Program Kerja:**\n• Siaga Pos Medis Upacara Bendera setiap hari Senin.\n• Aksi Donor Darah Sukarela rutin bekerjasama dengan UTD PMI Kab. Bogor.\n• Pembagian Tablet Tambah Darah (TTD) dan Posyandu Remaja.\n\nKakak dapat menanyakan nama beliau (misal: *'Biodata Sarah Azhari'* atau *'Biodata Anisa Rahma'*) untuk melihat KTA digital resminya!";
        }

        // 4. Bidang Humas
        if (str_contains($lower, 'humas') || str_contains($lower, 'hubungan masyarakat')) {
            if ($isAskingKetua && ! $isAskingAnggota) {
                return $this->getBiodataCard(
                    name: 'Dian Sastrowardoyo',
                    position: 'Ketua Bidang Hubungan Masyarakat (Humas)',
                    levelOrClass: 'Kelas XI',
                    idNumber: 'NRI: 24.49.044 PRT',
                    roleDescription: 'Hubungan eksternal dengan PMI Kabupaten Bogor, jejaring antar-pangkalan PMR se-Bogor Raya, serta publikasi berita kemanusiaan.',
                    motto: 'Menyuarakan pesan kemanusiaan dan merajut persaudaraan tanpa batas.',
                    title: 'BIODATA KETUA BIDANG HUMAS'
                );
            }

            return "Halo Teman Relawan! 📢\n\nBerikut adalah susunan kepengurusan lengkap **Bidang Hubungan Masyarakat (Humas)** PMR Wira SMAN 1 Ciawi Masa Bakti Ragana Dwi Pantara 2026/2027:\n\n👑 **Ketua Bidang Humas:**\n• **Dian Sastrowardoyo** (Kelas XI • NRI: 24.49.044 PRT)\n  *Tugas:* Hubungan eksternal dengan PMI Kabupaten Bogor, jejaring antar-pangkalan PMR se-Bogor Raya, serta publikasi berita kemanusiaan.\n  *Motto:* 'Menyuarakan pesan kemanusiaan dan merajut persaudaraan tanpa batas.'\n\n👥 **Staf Anggota Bidang Humas:**\n1. **Gilang Ramadhan** (Kelas X-1 • NRI: 25.50.045 PRT)\n   *Tugas:* Pengelolaan media sosial resmi organisasi, dokumentasi buletin mading sekolah, serta penyebaran info kegiatan relawan.\n   *Motto:* 'Kreatif menyampaikan aksi relawan demi menginspirasi generasi muda.'\n2. **Tiara Andini** (Kelas XI-4 • NRI: 24.49.049 PRT)\n   *Tugas:* Penghubung komunikasi antar-ekstrakurikuler sekolah, pendataan relawan tamu saat latihan gabungan, dan sosialisasi program UKS.\n   *Motto:* 'Komunikasi ramah dan hangat mempererat ikatan persahabatan relawan.'\n3. **Kevin Julio** (Kelas X-3 • NRI: 25.50.052 PRT)\n   *Tugas:* Pengelolaan layanan informasi pengunjung, hotline pertanyaan calon anggota, serta publikasi agenda donor darah.\n   *Motto:* 'Menghubungkan kepedulian sosial dengan aksi kemanusiaan nyata.'\n\n🎯 **Fokus Program Kerja:**\n• Pengelolaan media sosial Instagram, TikTok, dan YouTube resmi PMR Wira.\n• Jejaring silaturahmi dengan Forum Remaja Palang Merah Indonesia (FORPIS).\n• Publikasi buletin mading kemanusiaan dan promosi kegiatan donor darah.\n\nKakak dapat menanyakan *'Biodata Dian Sastrowardoyo'* atau *'Biodata Gilang Ramadhan'* untuk melihat KTA resminya!";
        }

        // 5. Bidang Kreasi
        if (str_contains($lower, 'kreasi') || str_contains($lower, 'media kreatif') || str_contains($lower, 'kreatif')) {
            if ($isAskingKetua && ! $isAskingAnggota) {
                return $this->getBiodataCard(
                    name: 'Nicholas Saputra',
                    position: 'Ketua Bidang Media Kreatif & Publikasi',
                    levelOrClass: 'Kelas XI',
                    idNumber: 'NRI: 24.49.055 PRT',
                    roleDescription: 'Pengarah artistik publikasi, perancangan poster edukasi kesehatan, pembuatan video animasi P3K, dan desain KTA digital.',
                    motto: 'Kreativitas tanpa batas untuk mengedukasi dan menggerakkan empati kemanusiaan.',
                    title: 'BIODATA KETUA BIDANG KREASI'
                );
            }

            return "Halo Teman Relawan! 🎨\n\nBerikut adalah susunan kepengurusan lengkap **Bidang Media Kreatif & Publikasi (Kreasi)** PMR Wira SMAN 1 Ciawi Masa Bakti Ragana Dwi Pantara 2026/2027:\n\n👑 **Ketua Bidang Kreasi:**\n• **Nicholas Saputra** (Kelas XI • NRI: 24.49.055 PRT)\n  *Tugas:* Pengarah artistik publikasi, perancangan poster edukasi kesehatan, pembuatan video animasi P3K, dan desain KTA digital.\n  *Motto:* 'Kreativitas tanpa batas untuk mengedukasi dan menggerakkan empati kemanusiaan.'\n\n👥 **Staf Anggota Bidang Kreasi:**\n1. **Putri Marino** (Kelas X-2 • NRI: 25.50.058 PRT)\n   *Tugas:* Desain infografis pola hidup sehat (PHBS), ilustrasi pertolongan pertama, dan tata letak materi e-book organisasi.\n   *Motto:* 'Karya visual yang indah membawa pesan keselamatan untuk semua.'\n2. **Aldi Taher** (Kelas XI-5 • NRI: 24.49.061 PRT)\n   *Tugas:* Editing video kampanye donor darah, dokumentasi sinematik kegiatan diklatsar, dan desain merchandise relawan.\n   *Motto:* 'Semangat berkarya dan berinovasi demi syiar kepalangmerahan.'\n\n🎯 **Fokus Program Kerja:**\n• Desain infografis pertolongan pertama dan poster PHBS mingguan.\n• Produksi video edukasi RJP dan tandu darurat untuk edukasi publik.\n• Perancangan desain identitas visual, seragam kontingen, dan KTA digital.\n\nKakak dapat menanyakan *'Biodata Nicholas Saputra'* atau *'Biodata Putri Marino'* untuk melihat KTA resminya!";
        }

        return null;
    }

    /**
     * Cari biodata pimpinan atau anggota tertentu dari Pembina sampai seluruh staf 5 bidang.
     */
    public function findSpecificMemberBiodata(string $lower): ?string
    {
        // 1. Wakil Ketua PMR Wira (Cek sebelum Ketua Umum karena pertanyaan 'wakil ketua' mengandung kata 'ketua')
        if (str_contains($lower, 'wakil') || str_contains($lower, 'imam abdul') || str_contains($lower, 'jabbar')) {
            return $this->getBiodataCard(
                name: 'Imam Abdul Jabbar',
                position: 'Wakil Ketua Umum PMR Wira',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.012 PRT',
                roleDescription: 'Mendampingi Ketua Umum, mengoordinasikan internal 5 bidang kepengurusan, pengawasan kedisiplinan anggota, serta memimpin rapat evaluasi bulanan.',
                motto: 'Berani melangkah, setia mengabdi, dan menjaga soliditas korsa demi kemanusiaan.',
                title: 'BIODATA WAKIL KETUA UMUM PMR WIRA'
            );
        }

        // 2. Pembina PMR
        if (str_contains($lower, 'pembina') || str_contains($lower, 'abdul hamid') || str_contains($lower, 'mulyadi')) {
            return $this->getBiodataCard(
                name: 'Abdul Hamid Saputra, S.Hum., M.Pd.',
                position: 'Pembina PMR Wira SMAN 1 Ciawi',
                levelOrClass: 'Dewan Pembina / Guru Pembimbing',
                idNumber: 'NIP: 19820415 200801 1 008',
                roleDescription: 'Membimbing, mengarahkan, dan mengawasi seluruh program kerja kepalangmerahan, pembinaan karakter relawan, serta sinergi antara pihak sekolah dan PMI Cabang.',
                motto: 'Mendidik relawan muda yang berkarakter, berakhlak mulia, tangguh, dan tulus mengabdi bagi kemanusiaan.',
                title: 'BIODATA PEMBINA PMR WIRA'
            );
        }

        // 3. Bidang Markas (Ketua & Staf)
        if (str_contains($lower, 'indra gunawan') || (str_contains($lower, 'ketua') && str_contains($lower, 'markas'))) {
            return $this->getBiodataCard(
                name: 'Indra Gunawan',
                position: 'Ketua Bidang Markas & Logistik UKS',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.029 PRT',
                roleDescription: 'Koordinator sarana prasarana UKS, pengecekan berkala masa kedaluwarsa obat, pemeliharaan tandu darurat & spalk bidai, serta jadwal piket markas.',
                motto: 'Markas yang tertata dan siap siaga adalah cerminan respon medis yang tanggap.',
                title: 'BIODATA KETUA BIDANG MARKAS'
            );
        }
        if (str_contains($lower, 'rian ardiansyah') || (str_contains($lower, 'rian') && (str_contains($lower, 'markas') || str_contains($lower, 'staf')))) {
            return $this->getBiodataCard(
                name: 'Rian Ardiansyah',
                position: 'Staf Anggota Bidang Markas',
                levelOrClass: 'Kelas X-1',
                idNumber: 'NRI: 25.50.007 PRT',
                roleDescription: 'Perawatan alat evakuasi tandu bambu, penyusunan stok kasa & mitela steril, serta kebersihan ruang posko UKS.',
                motto: 'Siap sedia merawat sarana evakuasi untuk keselamatan bersama.',
                title: 'BIODATA STAF BIDANG MARKAS'
            );
        }
        if (str_contains($lower, 'siti fatimah')) {
            return $this->getBiodataCard(
                name: 'Siti Fatimah',
                position: 'Staf Anggota Bidang Markas',
                levelOrClass: 'Kelas X-3',
                idNumber: 'NRI: 25.50.014 PRT',
                roleDescription: 'Pencatatan inventaris obat masuk dan keluar, pemeriksaan kotak P3K kelas, dan kesiapan tabung oksigen UKS.',
                motto: 'Disiplin mengelola logistik medis demi kelancaran tugas kemanusiaan.',
                title: 'BIODATA STAF BIDANG MARKAS'
            );
        }
        if (str_contains($lower, 'deni prasetyo') || (str_contains($lower, 'deni') && str_contains($lower, 'markas'))) {
            return $this->getBiodataCard(
                name: 'Deni Prasetyo',
                position: 'Staf Anggota Bidang Markas',
                levelOrClass: 'Kelas XI-2',
                idNumber: 'NRI: 24.49.031 PRT',
                roleDescription: 'Pemeliharaan perangkat darurat, uji kekuatan simpul tandu, serta logistik simulasi mitigasi bencana sekolah.',
                motto: 'Ketangguhan fisik dan ketelitian merawat perlengkapan medis.',
                title: 'BIODATA STAF BIDANG MARKAS'
            );
        }

        // 4. Bidang Pelayanan (Ketua & Staf)
        if (str_contains($lower, 'sarah azhari') || (str_contains($lower, 'ketua') && str_contains($lower, 'pelayanan'))) {
            return $this->getBiodataCard(
                name: 'Sarah Azhari',
                position: 'Ketua Bidang Pelayanan Medis & Donor Darah',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.034 PRT',
                roleDescription: 'Penanggung jawab posko medis upacara bendera, koordinasi aksi donor darah bersama PMI Kab. Bogor, serta posyandu remaja sekolah.',
                motto: 'Melayani dengan empati, sigap menolong sesama tanpa pamrih.',
                title: 'BIODATA KETUA BIDANG PELAYANAN'
            );
        }
        if (str_contains($lower, 'anisa rahma')) {
            return $this->getBiodataCard(
                name: 'Anisa Rahma',
                position: 'Staf Anggota Bidang Pelayanan',
                levelOrClass: 'Kelas X-2',
                idNumber: 'NRI: 25.50.019 PRT',
                roleDescription: 'Piket medis harian UKS, pemeriksaan tanda vital (tensi darah, suhu tubuh), serta pendampingan siswa sakit di ruang isolasi.',
                motto: 'Tangan terampil, hati tulus melayani mereka yang membutuhkan.',
                title: 'BIODATA STAF BIDANG PELAYANAN'
            );
        }
        if (str_contains($lower, 'budi santoso')) {
            return $this->getBiodataCard(
                name: 'Budi Santoso',
                position: 'Staf Anggota Bidang Pelayanan',
                levelOrClass: 'Kelas XI-1',
                idNumber: 'NRI: 24.49.035 PRT',
                roleDescription: 'Tim evakuasi lapangan, pengawalan medis kegiatan olahraga & ekstrakurikuler, serta penanganan kasus cedera.',
                motto: 'Sigap dalam bertindak, tenang dalam pertolongan darurat.',
                title: 'BIODATA STAF BIDANG PELAYANAN'
            );
        }
        if (str_contains($lower, 'dewi sartika')) {
            return $this->getBiodataCard(
                name: 'Dewi Sartika',
                position: 'Staf Anggota Bidang Pelayanan',
                levelOrClass: 'Kelas X-5',
                idNumber: 'NRI: 25.50.028 PRT',
                roleDescription: 'Edukasi pencegahan anemia remaja putri (pembagian tablet tambah darah), pencatatan rekam medis harian UKS.',
                motto: 'Kemanusiaan adalah panggilan jiwa untuk meringankan penderitaan sesama.',
                title: 'BIODATA STAF BIDANG PELAYANAN'
            );
        }

        // 5. Bidang Diklat (Ketua & Staf)
        if (str_contains($lower, 'reza rahadian') || (str_contains($lower, 'ketua') && str_contains($lower, 'diklat'))) {
            return $this->getBiodataCard(
                name: 'Reza Rahadian',
                position: 'Ketua Bidang Pendidikan & Pelatihan (Diklat)',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.040 PRT',
                roleDescription: 'Penyusunan kurikulum 7 Materi Pokok PMR Wira, penyelenggaraan Diklatsar Ruang & Lapangan, serta pelatihan rutin mingguan.',
                motto: 'Pendidikan kepalangmerahan yang unggul mencetak relawan yang tangguh dan terampil.',
                title: 'BIODATA KETUA BIDANG DIKLAT'
            );
        }
        if (str_contains($lower, 'fajar pratama')) {
            return $this->getBiodataCard(
                name: 'Fajar Pratama',
                position: 'Staf Anggota Bidang Diklat',
                levelOrClass: 'Kelas X-4',
                idNumber: 'NRI: 25.50.033 PRT',
                roleDescription: 'Instruktur materi pertolongan pertama dasar, pembimbingan simulasi RJP dan balut bidai bagi calon anggota baru.',
                motto: 'Belajar tiada henti demi pertolongan pertama yang tepat dan terstandar.',
                title: 'BIODATA STAF BIDANG DIKLAT'
            );
        }
        if (str_contains($lower, 'maya anggraini')) {
            return $this->getBiodataCard(
                name: 'Maya Anggraini',
                position: 'Staf Anggota Bidang Diklat',
                levelOrClass: 'Kelas XI-3',
                idNumber: 'NRI: 24.49.042 PRT',
                roleDescription: 'Pendampingan kontingen lomba ketangkasan PMR, modul evaluasi traveling kepalangmerahan, serta absensi latihan rutin.',
                motto: 'Semangat berbagi ilmu dan melatih sesama calon relawan.',
                title: 'BIODATA STAF BIDANG DIKLAT'
            );
        }

        // 6. Bidang Humas (Ketua & Staf)
        if (str_contains($lower, 'dian sastrowardoyo') || (str_contains($lower, 'ketua') && str_contains($lower, 'humas'))) {
            return $this->getBiodataCard(
                name: 'Dian Sastrowardoyo',
                position: 'Ketua Bidang Hubungan Masyarakat (Humas)',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.044 PRT',
                roleDescription: 'Hubungan eksternal dengan PMI Kabupaten Bogor, jejaring antar-pangkalan PMR se-Bogor Raya, serta publikasi berita kemanusiaan.',
                motto: 'Menyuarakan pesan kemanusiaan dan merajut persaudaraan tanpa batas.',
                title: 'BIODATA KETUA BIDANG HUMAS'
            );
        }
        if (str_contains($lower, 'gilang ramadhan')) {
            return $this->getBiodataCard(
                name: 'Gilang Ramadhan',
                position: 'Staf Anggota Bidang Humas',
                levelOrClass: 'Kelas X-1',
                idNumber: 'NRI: 25.50.045 PRT',
                roleDescription: 'Pengelolaan media sosial resmi organisasi, dokumentasi buletin mading sekolah, serta penyebaran info kegiatan relawan.',
                motto: 'Kreatif menyampaikan aksi relawan demi menginspirasi generasi muda.',
                title: 'BIODATA STAF BIDANG HUMAS'
            );
        }
        if (str_contains($lower, 'tiara andini')) {
            return $this->getBiodataCard(
                name: 'Tiara Andini',
                position: 'Staf Anggota Bidang Humas',
                levelOrClass: 'Kelas XI-4',
                idNumber: 'NRI: 24.49.049 PRT',
                roleDescription: 'Penghubung komunikasi antar-ekstrakurikuler sekolah, pendataan relawan tamu saat latihan gabungan, dan sosialisasi program UKS.',
                motto: 'Komunikasi ramah dan hangat mempererat ikatan persahabatan relawan.',
                title: 'BIODATA STAF BIDANG HUMAS'
            );
        }
        if (str_contains($lower, 'kevin julio')) {
            return $this->getBiodataCard(
                name: 'Kevin Julio',
                position: 'Staf Anggota Bidang Humas',
                levelOrClass: 'Kelas X-3',
                idNumber: 'NRI: 25.50.052 PRT',
                roleDescription: 'Pengelolaan layanan informasi pengunjung, hotline pertanyaan calon anggota, serta publikasi agenda donor darah.',
                motto: 'Menghubungkan kepedulian sosial dengan aksi kemanusiaan nyata.',
                title: 'BIODATA STAF BIDANG HUMAS'
            );
        }

        // 7. Bidang Kreasi (Ketua & Staf)
        if (str_contains($lower, 'nicholas saputra') || (str_contains($lower, 'ketua') && str_contains($lower, 'kreasi'))) {
            return $this->getBiodataCard(
                name: 'Nicholas Saputra',
                position: 'Ketua Bidang Media Kreatif & Publikasi',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.055 PRT',
                roleDescription: 'Pengarah artistik publikasi, perancangan poster edukasi kesehatan, pembuatan video animasi P3K, dan desain KTA digital.',
                motto: 'Kreativitas tanpa batas untuk mengedukasi dan menggerakkan empati kemanusiaan.',
                title: 'BIODATA KETUA BIDANG KREASI'
            );
        }
        if (str_contains($lower, 'putri marino')) {
            return $this->getBiodataCard(
                name: 'Putri Marino',
                position: 'Staf Anggota Bidang Kreasi',
                levelOrClass: 'Kelas X-2',
                idNumber: 'NRI: 25.50.058 PRT',
                roleDescription: 'Desain infografis pola hidup sehat (PHBS), ilustrasi pertolongan pertama, dan tata letak materi e-book organisasi.',
                motto: 'Karya visual yang indah membawa pesan keselamatan untuk semua.',
                title: 'BIODATA STAF BIDANG KREASI'
            );
        }
        if (str_contains($lower, 'aldi taher')) {
            return $this->getBiodataCard(
                name: 'Aldi Taher',
                position: 'Staf Anggota Bidang Kreasi',
                levelOrClass: 'Kelas XI-5',
                idNumber: 'NRI: 24.49.061 PRT',
                roleDescription: 'Editing video kampanye donor darah, dokumentasi sinematik kegiatan diklatsar, dan desain merchandise relawan.',
                motto: 'Semangat berkarya dan berinovasi demi syiar kepalangmerahan.',
                title: 'BIODATA STAF BIDANG KREASI'
            );
        }

        // 8. Ketua Umum PMR Wira
        if (str_contains($lower, 'ketua') || str_contains($lower, 'sulthan') || str_contains($lower, 'arkana')) {
            return $this->getBiodataCard(
                name: 'Sulthan Arkana Putra R.',
                position: 'Ketua Umum PMR Wira (Kabinet Ragana Dwi Pantara)',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.004 PRT',
                roleDescription: 'Memimpin jalannya organisasi, penanggung jawab umum program kerja kepalangmerahan, koordinasi pimpinan sekolah & PMI, serta mengarahkan 5 bidang operasional relawan.',
                motto: 'Siamo Tutti Fratelli — Memimpin dengan keteladanan, melayani sesama dengan ketulusan hati.',
                title: 'BIODATA KETUA UMUM PMR WIRA'
            );
        }

        // 9. Sekretaris Umum
        if (str_contains($lower, 'sekretaris') || str_contains($lower, 'putri raisya') || str_contains($lower, 'anjani')) {
            return $this->getBiodataCard(
                name: 'Putri Raisya Anjani',
                position: 'Sekretaris Umum (BPH)',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.018 PRT',
                roleDescription: 'Pengelolaan administrasi resmi, persuratan mandat lomba & dinas, pengarsipan notulensi rapat, pendataan buku induk anggota serta KTA digital relawan.',
                motto: 'Tertib administrasi wujud integritas dan dedikasi relawan.',
                title: 'BIODATA SEKRETARIS UMUM PMR WIRA'
            );
        }

        // 10. Bendahara Umum
        if (str_contains($lower, 'bendahara') || str_contains($lower, 'nisa nurul') || str_contains($lower, 'aulia')) {
            return $this->getBiodataCard(
                name: 'Nisa Nurul Aulia',
                position: 'Bendahara Umum (BPH)',
                levelOrClass: 'Kelas XI',
                idNumber: 'NRI: 24.49.023 PRT',
                roleDescription: 'Manajemen perbendaharaan kas organisasi, pembukuan keuangan iuran, penganggaran logistik medis UKS, serta pelaporan transparansi dana kegiatan kemanusiaan.',
                motto: 'Amanah, transparan, dan akuntabel dalam mengelola dana kemanusiaan.',
                title: 'BIODATA BENDAHARA UMUM PMR WIRA'
            );
        }

        return null;
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
    public function renderFullOrganizationStructure(): string
    {
        return "Halo Sahabat Kemanusiaan! 🏛️\n\n"
            ."Berikut adalah **Struktur Organisasi Resmi PMR Wira SMA Negeri 1 Ciawi** (Masa Bakti Ragana Dwi Pantara 2026/2027) dari Pembina sampai seluruh staf anggota:\n\n"
            ."👔 **LEVEL 1: DEWAN PEMBINA PMR**\n"
            ."• **Pembina Utama:** Abdul Hamid Saputra, S.Hum., M.Pd. (NIP: 19820415 200801 1 008)\n"
            ."• **Dewan Pembina Kehormatan:** Drs. H. Mulyadi, M.Pd.\n\n"
            ."👑 **LEVEL 2: PIMPINAN UTAMA**\n"
            ."• **Ketua Umum:** Sulthan Arkana Putra R. (Kelas XI • NRI: 24.49.004 PRT)\n\n"
            ."🛡️ **LEVEL 3: WAKIL PIMPINAN**\n"
            ."• **Wakil Ketua Umum:** Imam Abdul Jabbar (Kelas XI • NRI: 24.49.012 PRT)\n\n"
            ."📋 **LEVEL 4: BADAN PENGURUS HARIAN (BPH)**\n"
            ."• **Sekretaris Umum:** Putri Raisya Anjani (Kelas XI • NRI: 24.49.018 PRT)\n"
            ."• **Bendahara Umum:** Nisa Nurul Aulia (Kelas XI • NRI: 24.49.023 PRT)\n\n"
            ."🏢 **LEVEL 5: 5 BIDANG & SELURUH STAF ANGGOTA**\n\n"
            ."1. 📦 **Bidang Markas (Logistik & UKS):**\n"
            ."   • **Ketua Bidang:** Indra Gunawan (Kelas XI • NRI: 24.49.029 PRT)\n"
            ."   • **Staf Anggota:**\n"
            ."     - Rian Ardiansyah (Kelas X-1 • NRI: 25.50.007 PRT)\n"
            ."     - Siti Fatimah (Kelas X-3 • NRI: 25.50.014 PRT)\n"
            ."     - Deni Prasetyo (Kelas XI-2 • NRI: 24.49.031 PRT)\n\n"
            ."2. 🩹 **Bidang Pelayanan (Pos Medis & Donor Darah):**\n"
            ."   • **Ketua Bidang:** Sarah Azhari (Kelas XI • NRI: 24.49.034 PRT)\n"
            ."   • **Staf Anggota:**\n"
            ."     - Anisa Rahma (Kelas X-2 • NRI: 25.50.019 PRT)\n"
            ."     - Budi Santoso (Kelas XI-1 • NRI: 24.49.035 PRT)\n"
            ."     - Dewi Sartika (Kelas X-5 • NRI: 25.50.028 PRT)\n\n"
            ."3. 📚 **Bidang Diklat (Pendidikan & Pelatihan):**\n"
            ."   • **Ketua Bidang:** Reza Rahadian (Kelas XI • NRI: 24.49.040 PRT)\n"
            ."   • **Staf Anggota:**\n"
            ."     - Fajar Pratama (Kelas X-4 • NRI: 25.50.033 PRT)\n"
            ."     - Maya Anggraini (Kelas XI-3 • NRI: 24.49.042 PRT)\n\n"
            ."4. 📢 **Bidang Humas (Hubungan Masyarakat & Kemitraan):**\n"
            ."   • **Ketua Bidang:** Dian Sastrowardoyo (Kelas XI • NRI: 24.49.044 PRT)\n"
            ."   • **Staf Anggota:**\n"
            ."     - Gilang Ramadhan (Kelas X-1 • NRI: 25.50.045 PRT)\n"
            ."     - Tiara Andini (Kelas XI-4 • NRI: 24.49.049 PRT)\n"
            ."     - Kevin Julio (Kelas X-3 • NRI: 25.50.052 PRT)\n\n"
            ."5. 🎨 **Bidang Kreasi (Media Kreatif & Publikasi):**\n"
            ."   • **Ketua Bidang:** Nicholas Saputra (Kelas XI • NRI: 24.49.055 PRT)\n"
            ."   • **Staf Anggota:**\n"
            ."     - Putri Marino (Kelas X-2 • NRI: 25.50.058 PRT)\n"
            ."     - Aldi Taher (Kelas XI-5 • NRI: 24.49.061 PRT)\n\n"
            .'✨ *Kakak dapat menyebutkan nama pengurus atau staf di atas (misal: "Biodata Putri Raisya" atau "Biodata Rian Ardiansyah") untuk melihat kartu biodata digital resminya!*';
    }
}
