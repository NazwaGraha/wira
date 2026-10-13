<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class OrganizationGuideline extends Model
{
    protected $fillable = [
        'period',
        'established_date',
        'established_place',
        'gbho_policy_heading',
        'gbho_policy_text',
        'gbho_pdf_path',
        'ppuo_pdf_path',
        'signer_presidium',
        'signer_pembina',
        'signer_school',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function strategies(): HasMany
    {
        return $this->hasMany(GbhoStrategy::class, 'organization_guideline_id')->orderBy('order_number');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(PpuoChapter::class, 'organization_guideline_id')->orderBy('order_number');
    }

    /**
     * Get or initialize default instance with default data.
     */
    public static function current(): self
    {
        $item = self::with(['strategies', 'chapters'])->first();

        if (! $item) {
            $item = self::create([
                'period' => '2026/2027',
                'established_date' => '05 Januari 2026',
                'established_place' => 'Ciawi, Bogor',
                'gbho_policy_heading' => 'Arah Kebijakan Utama',
                'gbho_policy_text' => 'Mewujudkan PMR Wira SMAN 1 Ciawi sebagai organisasi yang aktif, solid, cerdas berorganisasi, dan memiliki ketangguhan fisik serta mental dalam menjalankan aksi kemanusiaan yang cepat, tepat, dan berdampak bagi lingkungan sekolah maupun masyarakat.',
                'gbho_pdf_path' => 'documents/GBHO_PMR_WIRA_SMAN_1_CIAWI_2026.pdf',
                'ppuo_pdf_path' => 'documents/PPUO_PMR_WIRA_SMAN_1_CIAWI_2026.pdf',
                'signer_presidium' => 'Presidium Sidang I, II, dan III',
                'signer_pembina' => 'Pembina PMR Wira SMAN 1 Ciawi',
                'signer_school' => 'Kepala SMA Negeri 1 Ciawi',
                'is_published' => true,
            ]);

            $defaultStrategies = [
                [
                    'order_number' => 1,
                    'title' => 'Kedisiplinan & Sikap Dasar Relawan',
                    'description' => 'Menanamkan rasa tanggung jawab, ketepatan waktu, dan kepatuhan terhadap aturan organisasi serta etika kepalangmerahan.',
                    'icon' => 'fa-solid fa-clock',
                ],
                [
                    'order_number' => 2,
                    'title' => 'Pelatihan Medis & Penanganan Darurat',
                    'description' => 'Mengasah keterampilan pertolongan pertama (PP), evakuasi, dan tanggap darurat secara berkala dan terstandarisasi.',
                    'icon' => 'fa-solid fa-kit-medical',
                ],
                [
                    'order_number' => 3,
                    'title' => 'Pembinaan Fisik & Mental Tangguh',
                    'description' => 'Menjaga kebugaran fisik dan ketahanan mental anggota melalui latihan terprogram serta pembiasaan disiplin lapangan.',
                    'icon' => 'fa-solid fa-dumbbell',
                ],
                [
                    'order_number' => 4,
                    'title' => 'Kekompakan & Solidaritas Internal',
                    'description' => 'Membangun rasa kekeluargaan dan saling percaya antaranggota tanpa membedakan tingkatan angkatan maupun seksi.',
                    'icon' => 'fa-solid fa-people-group',
                ],
                [
                    'order_number' => 5,
                    'title' => 'Manajemen Organisasi & Tertib Administrasi',
                    'description' => 'Menjalankan organisasi secara terstruktur, transparan, rapi administrasi, dan kepemimpinan adaptif.',
                    'icon' => 'fa-solid fa-list-check',
                ],
                [
                    'order_number' => 6,
                    'title' => 'Sinergi Pembina, Sekolah & Alumni',
                    'description' => 'Mempererat koordinasi intensif dengan Pembina, pihak sekolah, PMI Cabang Kabupaten Bogor, dan jejaring alumni PMR.',
                    'icon' => 'fa-solid fa-handshake',
                ],
            ];

            foreach ($defaultStrategies as $ds) {
                $item->strategies()->create($ds);
            }

            $defaultChapters = [
                [
                    'order_number' => 1,
                    'chapter_number' => 'BAB I',
                    'title' => 'Nama, Waktu, dan Kedudukan',
                    'summary' => 'Ketentuan identitas resmi unit, hari pembentukan, dan kedudukan di bawah OSIS serta naungan PMI.',
                    'badge' => 'Identitas Resmi',
                ],
                [
                    'order_number' => 2,
                    'chapter_number' => 'BAB II',
                    'title' => 'Asas, Prinsip, Landasan, dan Sifat',
                    'summary' => '7 Prinsip Palang Merah, Tri Bakti PMR, Pancasila & UUD 1945, serta sifat independen non-politik.',
                    'badge' => 'Landasan Moral',
                ],
                [
                    'order_number' => 3,
                    'chapter_number' => 'BAB III',
                    'title' => 'Visi, Misi, dan Tujuan Organisasi',
                    'summary' => 'Arah perjuangan organisasi mencetak generasi relawan berakhlak mulia dan tanggap kemanusiaan.',
                    'badge' => 'Arah Tujuan',
                ],
                [
                    'order_number' => 4,
                    'chapter_number' => 'BAB IV',
                    'title' => 'Keanggotaan & Hak/Kewajiban',
                    'summary' => 'Klasifikasi Calon Anggota, Anggota Penuh, dan Alumni; syarat hak suara dan kewajiban menjaga nama baik.',
                    'badge' => 'Keanggotaan',
                ],
                [
                    'order_number' => 5,
                    'chapter_number' => 'BAB V',
                    'title' => 'Atribut, Seragam, dan Lambang Resmi',
                    'summary' => 'Ketentuan Syal Kuning Segitiga (120x90x90cm), badge PMI, seragam PDH putih abu dan PDL lapangan.',
                    'badge' => 'Seragam Baku',
                ],
                [
                    'order_number' => 6,
                    'chapter_number' => 'BAB VI',
                    'title' => 'Struktur Kepengurusan & BPH',
                    'summary' => 'Hierarki kepengurusan: Pembina, Ketua Umum, Wakil Ketua, Sekretaris, Bendahara, dan Ketua Bidang.',
                    'badge' => 'Hierarki Pengurus',
                ],
                [
                    'order_number' => 7,
                    'chapter_number' => 'BAB VII',
                    'title' => 'Tata Kelola Markas & Bedah UKS',
                    'summary' => 'Operasional ruang UKS, jadwal piket harian sekolah, pencatatan logistik obat dan penanganan siswa sakit.',
                    'badge' => 'Manajemen UKS',
                ],
                [
                    'order_number' => 8,
                    'chapter_number' => 'BAB VIII',
                    'title' => 'Permusyawaratan & Rapat Anggota (RAT)',
                    'summary' => 'Mekanisme RAT tahunan, LPJ kepengurusan, pemilihan pengurus baru, dan kuorum pengambilan keputusan.',
                    'badge' => 'Permusyawaratan',
                ],
                [
                    'order_number' => 9,
                    'chapter_number' => 'BAB IX',
                    'title' => 'Keuangan & Pendanaan Organisasi',
                    'summary' => 'Sumber kas organisasi dari iuran sukarela, subsidi kesiswaan, donasi halal, dan transparansi laporan berkala.',
                    'badge' => 'Tata Kelola Kas',
                ],
                [
                    'order_number' => 10,
                    'chapter_number' => 'BAB X',
                    'title' => 'Tata Tertib, Disiplin, dan Sanksi',
                    'summary' => 'Kode etik relawan dan tahapan sanksi pelanggaran: teguran lisan, SP tertulis, hingga pemberhentian.',
                    'badge' => 'Sanksi Disiplin',
                ],
                [
                    'order_number' => 11,
                    'chapter_number' => 'BAB XI',
                    'title' => 'Aturan Tambahan & Lembar Pengesahan',
                    'summary' => 'Ketentuan peralihan, amandemen AD/ART, dan lembar legalitas penandatanganan pengesahan resmi.',
                    'badge' => 'Pengesahan',
                ],
            ];

            foreach ($defaultChapters as $dc) {
                $item->chapters()->create($dc);
            }

            $item->load(['strategies', 'chapters']);
        }

        return $item;
    }

    /**
     * Get downloadable URL for GBHO PDF.
     */
    public function getGbhoPdfUrlAttribute(): string
    {
        if ($this->gbho_pdf_path && Storage::disk('public')->exists($this->gbho_pdf_path)) {
            return asset('storage/'.$this->gbho_pdf_path);
        }

        return asset('documents/GBHO_PMR_WIRA_SMAN_1_CIAWI_2026.pdf');
    }

    /**
     * Get downloadable URL for PPUO PDF.
     */
    public function getPpuoPdfUrlAttribute(): string
    {
        if ($this->ppuo_pdf_path && Storage::disk('public')->exists($this->ppuo_pdf_path)) {
            return asset('storage/'.$this->ppuo_pdf_path);
        }

        return asset('documents/PPUO_PMR_WIRA_SMAN_1_CIAWI_2026.pdf');
    }
}
