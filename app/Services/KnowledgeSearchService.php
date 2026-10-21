<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KnowledgeSearchService
{
    /**
     * Cache memori lokal untuk knowledge base topics.
     *
     * @var array<int, array<string, mixed>>|null
     */
    protected ?array $topicsCache = null;

    /**
     * Stopwords umum bahasa Indonesia untuk memfilter pencarian kata kunci.
     *
     * @var array<int, string>
     */
    protected const STOPWORDS = [
        'yang', 'untuk', 'pada', 'ke', 'dari', 'di', 'dan', 'atau', 'ini', 'itu',
        'dengan', 'adalah', 'yaitu', 'yakni', 'sebagai', 'oleh', 'karena', 'bila',
        'jika', 'apakah', 'siapa', 'apa', 'bagaimana', 'berapa', 'kapan', 'di mana',
        'dimana', 'saja', 'bisa', 'tolong', 'mohon', 'saya', 'kak', 'kakak', 'halo',
    ];

    /**
     * Ambil path ke file knowledge.json.
     */
    public function getStoragePath(): string
    {
        return storage_path('app/knowledge/knowledge.json');
    }

    /**
     * Reset cache topik pengetahuan dalam memori.
     */
    public function clearCache(): void
    {
        $this->topicsCache = null;
    }

    /**
     * Ambil seluruh topik knowledge base dari storage JSON atau fallback default.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getKnowledge(): array
    {
        if ($this->topicsCache !== null) {
            return $this->topicsCache;
        }

        $path = $this->getStoragePath();
        if (File::exists($path)) {
            try {
                $raw = File::get($path);
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && ! empty($decoded['topics']) && is_array($decoded['topics'])) {
                    $this->topicsCache = $decoded['topics'];

                    return $this->topicsCache;
                }
            } catch (\Throwable $e) {
                // Fallback ke built-in bundle jika ada isu pembacaan file
            }
        }

        $this->topicsCache = $this->getBundledFallbackTopics();

        return $this->topicsCache;
    }

    /**
     * Cari topik knowledge base paling relevan berdasarkan query pengguna (RAG Retrieval).
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, int $limit = 5): array
    {
        $allTopics = $this->getKnowledge();
        $queryLower = Str::lower($query);
        $tokens = $this->extractTokens($queryLower);

        $scored = [];

        foreach ($allTopics as $topic) {
            $score = $this->calculateScore($topic, $queryLower, $tokens);
            if ($score > 0) {
                $scored[] = [
                    'score' => $score,
                    'topic' => $topic,
                ];
            }
        }

        // Urutkan berdasarkan score tertinggi
        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $results = [];
        foreach (array_slice($scored, 0, $limit) as $item) {
            $results[] = $item['topic'];
        }

        return $results;
    }

    /**
     * Ambil string konteks terformat Markdown dari hasil pencarian knowledge base.
     */
    public function getRelevantContext(string $query, int $limit = 4): string
    {
        $results = $this->search($query, $limit);

        if (empty($results)) {
            // Jika pencarian kata kunci spesifik tidak menghasilkan hit, berikan ringkasan profil umum
            $results = array_slice($this->getKnowledge(), 0, 2);
        }

        $chunks = [];
        foreach ($results as $index => $topic) {
            $num = $index + 1;
            $title = $topic['title'] ?? 'Topik Informasi';
            $content = $topic['content'] ?? '';
            $chunks[] = "### [RAG-DOC-{$num}] {$title}\n{$content}";
        }

        return implode("\n\n", $chunks);
    }

    /**
     * Hitung skor relevansi topik terhadap query dan token pencarian.
     *
     * @param  array<string, mixed>  $topic
     * @param  array<int, string>  $tokens
     */
    protected function calculateScore(array $topic, string $queryLower, array $tokens): int
    {
        $score = 0;
        $titleLower = Str::lower((string) ($topic['title'] ?? ''));
        $contentLower = Str::lower((string) ($topic['content'] ?? ''));
        $keywords = array_map(fn ($k) => Str::lower((string) $k), (array) ($topic['keywords'] ?? []));

        // 1. Cek Exact Substring Match pada query
        foreach ($keywords as $kw) {
            if ($kw !== '' && str_contains($queryLower, $kw)) {
                $score += 25;
            }
        }

        // 2. Cek Kesesuaian Judul
        if (str_contains($titleLower, $queryLower)) {
            $score += 30;
        }

        // 3. Cek Token Overlap
        foreach ($tokens as $token) {
            if (strlen($token) < 2) {
                continue;
            }

            if (in_array($token, $keywords, true)) {
                $score += 15;
            }

            if (str_contains($titleLower, $token)) {
                $score += 10;
            }

            if (str_contains($contentLower, $token)) {
                $score += 4;
            }
        }

        // 4. Heuristik Khusus & Prioritas Tokoh / Istilah Spesifik
        if (str_contains($queryLower, 'pembina') && ($topic['id'] ?? '') === 'biodata_pembina') {
            $score += 50;
        }
        if ((str_contains($queryLower, 'wakil') || str_contains($queryLower, 'imam')) && ($topic['id'] ?? '') === 'biodata_wakil_ketua') {
            $score += 50;
        }
        if ((str_contains($queryLower, 'ketua umum') || str_contains($queryLower, 'ketua pmr') || str_contains($queryLower, 'sulthan')) && ! str_contains($queryLower, 'wakil') && ($topic['id'] ?? '') === 'biodata_ketua_umum') {
            $score += 50;
        }
        if (str_contains($queryLower, 'sekretaris') && ($topic['id'] ?? '') === 'biodata_sekretaris') {
            $score += 50;
        }
        if (str_contains($queryLower, 'bendahara') && ($topic['id'] ?? '') === 'biodata_bendahara') {
            $score += 50;
        }
        if (str_contains($queryLower, 'diklat') && (($topic['id'] ?? '') === 'member_reza-rahadian' || str_contains($topic['id'] ?? '', 'diklat'))) {
            $score += 80;
        }
        if (str_contains($queryLower, 'markas') && (($topic['id'] ?? '') === 'member_indra-gunawan' || str_contains($topic['id'] ?? '', 'markas'))) {
            $score += 80;
        }
        if (str_contains($queryLower, 'pelayanan') && (($topic['id'] ?? '') === 'member_sarah-azhari' || str_contains($topic['id'] ?? '', 'pelayanan'))) {
            $score += 80;
        }
        if (str_contains($queryLower, 'humas') && (($topic['id'] ?? '') === 'member_dian-sastrowardoyo' || str_contains($topic['id'] ?? '', 'humas'))) {
            $score += 80;
        }
        if (str_contains($queryLower, 'kreasi') && (($topic['id'] ?? '') === 'member_nicholas-saputra' || str_contains($topic['id'] ?? '', 'kreasi'))) {
            $score += 80;
        }
        if (str_contains($queryLower, 'mars') && ($topic['id'] ?? '') === 'mars_pmi') {
            $score += 60;
        }
        if ((str_contains($queryLower, 'tandu') || str_contains($queryLower, 'bambu')) && ($topic['id'] ?? '') === 'p3k_tandu_darurat') {
            $score += 60;
        }
        if ((str_contains($queryLower, 'rjp') || str_contains($queryLower, 'cpr')) && ($topic['id'] ?? '') === 'p3k_resusitasi_jantung_paru') {
            $score += 60;
        }
        if ((str_contains($queryLower, 'lomba') || str_contains($queryLower, 'sua bhakti')) && ($topic['id'] ?? '') === 'lomba_pmr_sua_bhakti') {
            $score += 50;
        }
        if (str_contains($queryLower, 'donor') && ($topic['id'] ?? '') === 'donor_darah_sukarela') {
            $score += 50;
        }
        if ((str_contains($queryLower, 'syal') || str_contains($queryLower, 'gbho') || str_contains($queryLower, 'ppuo')) && ($topic['id'] ?? '') === 'gbho_ppuo_2026') {
            $score += 50;
        }
        if ((str_contains($queryLower, 'henry dunant') || str_contains($queryLower, 'solferino')) && ($topic['id'] ?? '') === 'sejarah_gerakan_kepalangmerahan') {
            $score += 50;
        }
        if ((str_contains($queryLower, '7 prinsip') || str_contains($queryLower, 'tujuh prinsip')) && ($topic['id'] ?? '') === '7_prinsip_dasar_pmi') {
            $score += 50;
        }
        if (str_contains($queryLower, 'tri bakti') && ($topic['id'] ?? '') === 'tri_bakti_pmr') {
            $score += 50;
        }
        if ((str_contains($queryLower, 'kejang') || str_contains($queryLower, 'epilepsi') || str_contains($queryLower, 'step') || str_contains($queryLower, 'konvulsi')) && ($topic['id'] ?? '') === 'p3k_kejang_kejang') {
            $score += 70;
        }
        if ((str_contains($queryLower, 'tersedak') || str_contains($queryLower, 'choking') || str_contains($queryLower, 'heimlich')) && ($topic['id'] ?? '') === 'p3k_tersedak_choking') {
            $score += 70;
        }
        if ((str_contains($queryLower, 'asma') || str_contains($queryLower, 'sesak')) && ($topic['id'] ?? '') === 'p3k_asma_sesak_napas') {
            $score += 70;
        }
        if ((str_contains($queryLower, 'ular') || str_contains($queryLower, 'patok')) && ($topic['id'] ?? '') === 'p3k_gigitan_ular_bisa') {
            $score += 70;
        }
        if (str_contains($queryLower, 'syok') && ($topic['id'] ?? '') === 'p3k_syok_darurat') {
            $score += 70;
        }
        if (str_contains($queryLower, 'markas') && ($topic['id'] ?? '') === 'susunan_anggota_bidang_markas') {
            $score += 45;
        }
        if (str_contains($queryLower, 'pelayanan') && ($topic['id'] ?? '') === 'susunan_anggota_bidang_pelayanan') {
            $score += 45;
        }
        if (str_contains($queryLower, 'diklat') && ($topic['id'] ?? '') === 'susunan_anggota_bidang_diklat') {
            $score += 45;
        }
        if (str_contains($queryLower, 'humas') && ($topic['id'] ?? '') === 'susunan_anggota_bidang_humas') {
            $score += 45;
        }
        if (str_contains($queryLower, 'kreasi') && ($topic['id'] ?? '') === 'susunan_anggota_bidang_kreasi') {
            $score += 45;
        }
        if ((str_contains($queryLower, 'racun') || str_contains($queryLower, 'keracunan')) && ($topic['id'] ?? '') === 'p3k_keracunan') {
            $score += 70;
        }
        if ((str_contains($queryLower, 'triage') || str_contains($queryLower, 'triase') || str_contains($queryLower, 'start')) && ($topic['id'] ?? '') === 'ksr_triage_bencana') {
            $score += 70;
        }
        if ((str_contains($queryLower, 'hpi') || str_contains($queryLower, 'perikemanusiaan')) && ($topic['id'] ?? '') === 'ksr_hukum_perikemanusiaan_internasional') {
            $score += 70;
        }
        if ((str_contains($queryLower, 'posko') || str_contains($queryLower, 'manajemen posko')) && ($topic['id'] ?? '') === 'ksr_manajemen_posko') {
            $score += 70;
        }

        return $score;
    }

    /**
     * Ekstrak token kata penting dari teks query.
     *
     * @return array<int, string>
     */
    protected function extractTokens(string $text): array
    {
        // Bersihkan tanda baca
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $words = preg_split('/\s+/', (string) $clean, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $tokens = [];
        foreach ($words as $word) {
            $w = trim(Str::lower($word));
            if ($w !== '' && ! in_array($w, self::STOPWORDS, true) && strlen($w) > 1) {
                $tokens[] = $w;
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * Fallback topik bawaan jika storage JSON tidak terbaca.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getBundledFallbackTopics(): array
    {
        return [
            [
                'id' => 'profil_organisasi',
                'category' => 'organisasi',
                'title' => 'Profil & Identitas PMR Wira SMAN 1 Ciawi',
                'keywords' => ['profil', 'alamat', 'lokasi', 'kontak', 'email', 'markas', 'sekretariat', 'uks'],
                'content' => 'PMR Wira Unit SMA Negeri 1 Ciawi (Terakreditasi A) beralamat di Ruang UKS SMAN 1 Ciawi, Jl. Veteran No. 46, Pandansari, Ciawi, Bogor. Email: pmrwirasman1c@gmail.com. Hotline UKS: 0857-7024-6918. Masa bakti kepengurusan: Ragana Dwi Pantara 2026/2027.',
            ],
            [
                'id' => 'biodata_pembina',
                'category' => 'biodata',
                'title' => 'Biodata Pembina PMR Wira',
                'keywords' => ['pembina', 'abdul hamid', 'mulyadi', 'biodata pembina'],
                'content' => 'Pembina PMR Wira SMAN 1 Ciawi adalah Abdul Hamid Saputra, S.Hum., M.Pd. (NIP: 19820415 200801 1 008) dan Drs. H. Mulyadi, M.Pd.',
            ],
            [
                'id' => 'biodata_ketua_umum',
                'category' => 'biodata',
                'title' => 'Biodata Ketua Umum PMR Wira',
                'keywords' => ['ketua umum', 'ketua pmr', 'sulthan', 'arkana'],
                'content' => 'Ketua Umum PMR Wira SMAN 1 Ciawi adalah Sulthan Arkana Putra R. (Kelas XI • NRI: 24.49.004 PRT • Motto: Siamo Tutti Fratelli).',
            ],
        ];
    }
}
