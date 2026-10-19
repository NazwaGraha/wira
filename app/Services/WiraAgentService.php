<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WiraAgentService
{
    public const SYSTEM_PROMPT = <<<'PROMPT'
Anda adalah "Wira Agent", asisten virtual resmi berbasis AI dari Palang Merah Remaja (PMR) Wira SMA Negeri 1 Ciawi, Kabupaten Bogor (Masa Bakti Ragana Dwi Pantara 2026/2027).

Identitas & Persona:
- Nama: Wira Agent.
- Karakter: Ramah, sopan, komunikatif, berjiwa muda, semangat, disiplin, menjunjung tinggi 7 Prinsip Dasar PMI dan Tri Bakti PMR memiliki jiwa motivator atau inspirator.
- Sapaan: Gunakan panggilan santun seperti "Humanity", "Halo Kak / Teman Relawan" atau "Sahabat Kemanusiaan".

Pengetahuan Utama:
- Profil Organisasi: Unit PMR Wira SMAN 1 Ciawi terakreditasi A, beralamat di Ruang UKS/PMR SMAN 1 Ciawi, Jl. Veteran No. 46, Pandansari, Ciawi, Bogor. Email: pmrwirasman1c@gmail.com. Kontak Hotline Piket UKS: 0857-7024-6918.
- Program Unggulan: Donor Darah Sukarela rutin bersama PMI Kab. Bogor, pelatihan P3K (Pembalutan, Pembidaian, RJP/CPR, Tandu Darurat), dan mitigasi gempa/bencana sekolah.
- Informasi Lomba: Jika ditanya mengenai kegiatan lomba (seperti Ajang Prestasi PMR / Sua Bhakti Berkarya III), jelaskan cabang lomba umum (Pertolongan Pertama, Tandu Darurat, Cerdas Cermat/Traveling, Video Kemanusiaan) dan arahkan untuk memantau menu Lomba di website atau menghubungi panitia.
- Batasan: Berikan edukasi P3K dasar saja; jika kasus gawat darurat medis berat, selalu sarankan membawa korban ke Puskesmas/Rumah Sakit atau menghubungi tim medis darurat/Hotline UKS.
PROMPT;

    /**
     * Proses pesan dan hasilkan jawaban dari AI (Gemini / OpenAI / Fallback Engine).
     *
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{success: bool, reply: string, provider: string, timestamp: string}
     */
    public function chat(string $message, array $history = []): array
    {
        $geminiKey = (string) config('services.gemini.key');
        $openaiKey = (string) config('services.openai.key');

        // 1. Coba Google Gemini API jika key tersedia
        if (! empty($geminiKey)) {
            $geminiResponse = $this->callGemini($geminiKey, $message, $history);
            if ($geminiResponse !== null && trim($geminiResponse) !== '') {
                return [
                    'success' => true,
                    'reply' => $geminiResponse,
                    'provider' => 'gemini',
                    'timestamp' => now()->format('H:i'),
                ];
            }
        }

        // 2. Coba OpenAI API jika key tersedia
        if (! empty($openaiKey)) {
            $openaiResponse = $this->callOpenAi($openaiKey, $message, $history);
            if ($openaiResponse !== null && trim($openaiResponse) !== '') {
                return [
                    'success' => true,
                    'reply' => $openaiResponse,
                    'provider' => 'openai',
                    'timestamp' => now()->format('H:i'),
                ];
            }
        }

        // 3. Fallback cerdas: PMR Wira Knowledge Engine
        $fallbackReply = $this->generateKnowledgeResponse($message);

        return [
            'success' => true,
            'reply' => $fallbackReply,
            'provider' => 'knowledge_engine',
            'timestamp' => now()->format('H:i'),
        ];
    }

    /**
     * Panggil Google Gemini API (v1beta generateContent).
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function callGemini(string $apiKey, string $message, array $history): ?string
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
                        ['text' => self::SYSTEM_PROMPT],
                    ],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
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
    protected function callOpenAi(string $apiKey, string $message, array $history): ?string
    {
        try {
            $model = config('services.openai.model', 'gpt-4o-mini');
            $url = 'https://api.openai.com/v1/chat/completions';

            $messages = [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
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
                    'max_tokens' => 800,
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
     * Fallback Knowledge Engine ketika API Key belum diset atau kuota habis.
     */
    public function generateKnowledgeResponse(string $message): string
    {
        $lower = strtolower($message);

        // 1. Info Lomba PMR
        if (str_contains($lower, 'lomba') || str_contains($lower, 'sua bhakti') || str_contains($lower, 'kompetisi') || str_contains($lower, 'kejuaraan')) {
            return "Halo Sahabat Kemanusiaan! 🏆\n\nUntuk kegiatan perlombaan kepalangmerahan di PMR Wira SMAN 1 Ciawi, kami secara rutin berpartisipasi dan menyelenggarakan ajang bergengsi seperti **Sua Bhakti Berkarya**.\n\nBeberapa cabang lomba utama yang sering diperlombakan antara lain:\n1. **Pertolongan Pertama (PP):** Penanganan kasus medis, patah tulang, dan luka terbuka.\n2. **Pasang Tandu Darurat (Tandu Cepat):** Ketepatan, kekuatan ikatan, dan kecepatan evakuasi.\n3. **Traveling / Cerdas Cermat:** Wawasan kepalangmerahan, kepemimpinan, dan 7 Prinsip PMI.\n4. **Karya Video & Poster Kemanusiaan:** Sosialisasi isu kesehatan dan donor darah.\n\nKakak dapat memantau panduan juklak/juknis lengkap, daftar online, dan rekap juara di menu **Lomba PMR** pada website ini! Semangat mengukir prestasi! ✨";
        }

        // 2. Info Donor Darah
        if (str_contains($lower, 'donor') || str_contains($lower, 'darah') || str_contains($lower, 'transfusi') || str_contains($lower, 'stok')) {
            return "Humanity! Setetes darah kita adalah nyawa bagi sesama. 🩸\n\nPMR Wira SMAN 1 Ciawi secara rutin menggelar **Aksi Donor Darah Sukarela** bekerja sama dengan Unit Transfusi Darah (UTD) PMI Kabupaten Bogor.\n\n**Syarat Umum Menjadi Pendonor:**\n• Usia minimal 17 tahun (atau telah memiliki KTP).\n• Berat badan minimal 45 kg.\n• Tekanan darah normal (Sistole 100-160, Diastole 70-100 mmHg).\n• Kadar Hemoglobin (Hb) minimal 12,5 g/dL.\n• Sehat jasmani dan tidak sedang mengonsumsi obat keras 3 hari terakhir.\n\nInformasi jadwal pelaksanaan dan form pendaftaran online pendonor dapat dicek langsung pada menu **Donor Darah** di website resmi kami. Mari berbagi kehidupan! ❤️";
        }

        // 3. Pertolongan Pertama (P3K)
        if (str_contains($lower, 'p3k') || str_contains($lower, 'pertolongan') || str_contains($lower, 'pingsan') || str_contains($lower, 'luka') || str_contains($lower, 'patah') || str_contains($lower, 'rjp') || str_contains($lower, 'cpr') || str_contains($lower, 'mimisan')) {
            return "Halo Teman Relawan! 🩹\n\nDalam prinsip **Pertolongan Pertama (P3K)** PMR Wira, langkah utama selalu mengutamakan **3A (Aman Diri, Aman Pasien, Aman Lingkungan)**.\n\n**Panduan Tindakan Cepat Dasar:**\n• **Pasien Pingsan:** Bawa ke tempat teduh/berventilasi baik, baringkan terlentang, longgarkan pakaian ketat, dan tinggikan posisi tungkai kaki sekitar 20-30 cm agar aliran darah ke otak lancar.\n• **Luka Pendarahan:** Lakukan penekanan langsung (*direct pressure*) pada luka menggunakan kassa steril, lalu balut dengan perban/mitela.\n• **Mimisan:** Duduk condong ke depan, tekan cuping hidung selama 5-10 menit, bernapas lewat mulut (jangan mendongak ke belakang).\n\n⚠️ **Catatan Keselamatan:** Jika korban tidak sadar, sesak napas berat, cedera leher/tulang belakang, atau pendarahan masif, segera panggil tim medis ke Ruang UKS melalui **Hotline UKS (0857-7024-6918)** atau rujuk ke Puskesmas/RS terdekat!";
        }

        // 4. Syarat Gabung Relawan / Pendaftaran
        if (str_contains($lower, 'gabung') || str_contains($lower, 'daftar') || str_contains($lower, 'relawan') || str_contains($lower, 'rekrutmen') || str_contains($lower, 'anggota') || str_contains($lower, 'ekskul')) {
            return "Selamat datang Sahabat Kemanusiaan! Bangga sekali melihat antusiasmemu untuk mengabdi bersama kami! 🤝\n\n**Syarat Bergabung PMR Wira SMAN 1 Ciawi (Masa Bakti Ragana Dwi Pantara):**\n1. Terdaftar aktif sebagai siswa-siswi SMA Negeri 1 Ciawi (Kelas X atau XI).\n2. Memiliki jiwa kerelawanan, kepedulian sosial, dan dedikasi tinggi.\n3. Bersedia mengikuti Diklat Dasar (Diklatsar) Ruang dan Lapangan.\n4. Memperoleh surat persetujuan dari orang tua/wali siswa.\n\nPendaftaran dibuka setiap awal tahun ajaran baru atau saat masa orientasi ekskul (MPLS/Demo Ekskul). Kakak juga bisa mengisi formulir pra-pendaftaran di halaman **Kontak & Gabung Relawan** pada website kami. Kami tunggu kiprahmu di keluarga besar PMR Wira SMANIC! 🌟";
        }

        // 5. Profil / Kontak / UKS
        if (str_contains($lower, 'profil') || str_contains($lower, 'alamat') || str_contains($lower, 'lokasi') || str_contains($lower, 'kontak') || str_contains($lower, 'sekretariat') || str_contains($lower, 'uks')) {
            return "Halo Sahabat Relawan! 🏫\n\nBerikut identitas resmi markas kami:\n• **Organisasi:** Palang Merah Remaja (PMR) Wira Unit SMAN 1 Ciawi (Terakreditasi A).\n• **Masa Bakti Saat Ini:** Ragana Dwi Pantara 2026/2027.\n• **Alamat Pos/Markas:** Ruang UKS/PMR SMA Negeri 1 Ciawi, Jl. Veteran No. 46, Pandansari, Kec. Ciawi, Kabupaten Bogor, Jawa Barat (16720).\n• **Email Resmi:** pmrwirasman1c@gmail.com\n• **Hotline Siaga UKS:** 0857-7024-6918 (Buka Setiap Hari Sekolah 07.00 - 16.00 WIB)\n\nPintu markas kami selalu terbuka hangat untuk teman-teman yang ingin berdiskusi, belajar pertolongan pertama, atau bersilaturahmi. Siamo Tutti Fratelli! ❤️";
        }

        // 6. Prinsip & Tri Bakti
        if (str_contains($lower, 'prinsip') || str_contains($lower, 'tri bakti') || str_contains($lower, '7 prinsip') || str_contains($lower, 'semboyan')) {
            return "Siamo Tutti Fratelli — Kita Semua Bersaudara! 🕊️\n\nSebagai anggota Palang Merah Remaja, kami berpegang teguh pada:\n\n**7 Prinsip Dasar Gerakan Palang Merah & Bulan Sabit Merah Internasional:**\n1. Kemanusiaan (*Humanity*)\n2. Kesamaan (*Impartiality*)\n3. Kenetralan (*Neutrality*)\n4. Kemandirian (*Independence*)\n5. Kesukarelaan (*Voluntary Service*)\n6. Kesatuan (*Unity*)\n7. Kesemestaan (*Universality*)\n\n**Tri Bakti PMR:**\n1. Meningkatkan keterampilan hidup sehat.\n2. Berkarya dan berbakti di masyarakat.\n3. Mempererat persahabatan nasional dan internasional.\n\nNilai-nilai ini yang membentuk karakter tangguh, disiplin, dan berempati tinggi pada setiap relawan PMR Wira SMAN 1 Ciawi!";
        }

        // Default salam ramah motivator
        return "Halo Sahabat Kemanusiaan! Salam hangat dari Wira Agent. 🌟\n\nSaya asisten virtual resmi PMR Wira SMA Negeri 1 Ciawi yang siap membantu Kakak seputar informasi organisasi kami.\n\nAda yang bisa saya bantu hari ini? Kakak bisa menanyakan tentang:\n• 🏆 **Info Lomba PMR** (Sua Bhakti Berkarya)\n• 🩸 **Jadwal & Syarat Donor Darah**\n• 🩹 **Panduan Dasar Pertolongan Pertama (P3K)**\n• 📋 **Cara Bergabung Menjadi Relawan PMR**\n• 🏫 **Pos Medis & Hotline UKS Sekolah**\n\nSilakan ketik pertanyaan atau klik tombol saran di bawah ya! Tetap semangat dan selalu junjung tinggi rasa kemanusiaan! ❤️";
    }
}
