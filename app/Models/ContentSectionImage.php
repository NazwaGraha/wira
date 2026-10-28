<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ContentSectionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'title',
        'tagline',
        'tagline_icon',
        'headline',
        'quote_text',
        'body_text',
        'body_text_secondary',
        'checklist_items',
        'cards_data',
        'vision_text',
        'mission_items',
        'button_text',
        'button_url',
        'page_name',
        'image_path',
        'badge_text',
        'badge_subtext',
        'badge_icon',
        'description',
    ];

    protected $casts = [
        'checklist_items' => 'array',
        'cards_data' => 'array',
        'mission_items' => 'array',
    ];

    /**
     * Fallback getters untuk memastikan tampilan visual 100% konsisten dengan desain beranda & tentang kami.
     */
    public function getDisplayTaglineAttribute(): string
    {
        if (! empty($this->tagline)) {
            return $this->tagline;
        }

        return match ($this->section_key) {
            'home_pillars' => 'Pilar Aksi Kemanusiaan',
            'about_intro' => 'MENGENAL PMR',
            'about_tri_bakti' => 'Tri Bakti PMR',
            'about_principles' => 'Landasan Moral Universal',
            'about_vision_mission' => 'Arah Gerak Organisasi',
            default => 'Profil Ekstrakurikuler',
        };
    }

    public function getDisplayTaglineIconAttribute(): string
    {
        if (! empty($this->tagline_icon)) {
            return $this->tagline_icon;
        }

        return match ($this->section_key) {
            'about_intro' => 'fa-solid fa-book-open-reader',
            'about_tri_bakti' => 'fa-solid fa-hands-holding-child',
            'about_vision_mission' => 'fa-solid fa-compass',
            default => 'fa-solid fa-shield-heart',
        };
    }

    public function getDisplayHeadlineAttribute(): string
    {
        if (! empty($this->headline)) {
            return $this->headline;
        }

        return match ($this->section_key) {
            'home_pillars' => 'Program Unggulan PMR Wira',
            'about_intro' => 'Profil PMR Wira SMAN 1 Ciawi',
            'about_tri_bakti' => 'Tri Bakti Palang Merah Remaja',
            'about_principles' => '7 Dasar Prinsip Palang Merah',
            'about_vision_mission' => 'Visi & Misi Kami',
            default => 'Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi',
        };
    }

    public function getDisplayQuoteTextAttribute(): string
    {
        if (! empty($this->quote_text)) {
            return $this->quote_text;
        }

        return 'Palang Merah Remaja atau PMR adalah suatu organisasi binaan dari Palang Merah Indonesia yang berpusat di sekolah-sekolah ataupun kelompok-kelompok masyarakat (sanggar, kelompok belajar, dll.) yang bertujuan membangun dan mengembangkan karakter Kepalangmerahan agar siap menjadi Relawan PMI pada masa depan.';
    }

    public function getDisplayBodyTextAttribute(): string
    {
        if (! empty($this->body_text)) {
            return $this->body_text;
        }

        return match ($this->section_key) {
            'home_pillars' => 'Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.',
            'about_intro' => 'Di SMA Negeri 1 Ciawi, unit PMR Wira berperan aktif sebagai garda kemanusiaan di lingkungan sekolah, siaga memberikan pertolongan pertama, mempromosikan perilaku hidup bersih dan sehat, serta membina kepedulian sosial melalui aksi donor darah sukarela dan tanggap darurat bencana.',
            'about_tri_bakti' => 'Tiga pilar kewajiban utama yang senantiasa diamalkan oleh seluruh anggota PMR Wira SMAN 1 Ciawi dalam kehidupan sehari-hari:',
            'about_principles' => 'Tujuh prinsip fundamental yang menjadi pedoman teguh setiap langkah dan tindakan relawan PMR Wira di seluruh dunia:',
            default => 'Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA. Kami berkomitmen menanamkan nilai-nilai Tri Bakti PMR serta 7 Prinsip Dasar Palang Merah dan Bulan Sabit Merah Internasional.',
        };
    }

    public function getDisplayBodyTextSecondaryAttribute(): string
    {
        if (! empty($this->body_text_secondary)) {
            return $this->body_text_secondary;
        }

        return 'Masa bakti Ragana Dwi Pantara 2026/2027 berkomitmen mengamalkan seluruh nilai kepalangmerahan ini secara profesional, modern, dan penuh dedikasi bagi seluruh warga sekolah dan masyarakat sekitar.';
    }

    public function getDisplayBadgeIconAttribute(): string
    {
        return ! empty($this->badge_icon) ? $this->badge_icon : 'fa-solid fa-shield-halved';
    }

    public function getDisplayVisionTextAttribute(): string
    {
        if (! empty($this->vision_text)) {
            return $this->vision_text;
        }

        return 'Terwujudnya PMR Wira SMAN 1 Ciawi sebagai unit relawan muda yang berkarakter kemanusiaan tangguh, sigap, berintegritas, mandiri, dan berprestasi berlandaskan prinsip-prinsip dasar Palang Merah Internasional.';
    }

    public function getDisplayMissionItemsAttribute(): array
    {
        if (! empty($this->mission_items) && is_array($this->mission_items)) {
            $filtered = array_values(array_filter($this->mission_items, fn ($item) => filled(trim((string) $item))));
            if (! empty($filtered)) {
                return $filtered;
            }
        }

        return [
            'Menanamkan pemahaman dan pengamalan 7 Prinsip Dasar Palang Merah dan Tri Bakti PMR.',
            'Meningkatkan kapasitas anggota dalam keterampilan pertolongan pertama, sanitasi kesehatan, dan penanggulangan bencana.',
            'Aktif menyelenggarakan program donor darah sukarela dan bakti sosial bagi masyarakat.',
            'Menjalin sinergi erat dengan pembina sekolah, PMI Kabupaten Bogor, dan unit PMR lainnya.',
        ];
    }

    public function getDisplayTriBaktiAttribute(): array
    {
        if (! empty($this->cards_data) && is_array($this->cards_data)) {
            $cleaned = [];
            foreach ($this->cards_data as $idx => $card) {
                if (! empty($card['title'])) {
                    $cleaned[] = [
                        'sub_badge' => ! empty($card['sub_badge']) ? $card['sub_badge'] : ('Tri Bakti '.($idx + 1)),
                        'icon' => ! empty($card['icon']) ? $card['icon'] : 'fa-solid fa-hand-holding-heart',
                        'title' => $card['title'],
                        'description' => $card['description'] ?? '',
                    ];
                }
            }
            if (! empty($cleaned)) {
                return $cleaned;
            }
        }

        return [
            [
                'sub_badge' => 'Tri Bakti 1',
                'icon' => 'fa-solid fa-hand-holding-heart',
                'title' => 'Berbakti kepada Masyarakat',
                'description' => 'Turut serta dalam aksi kemanusiaan di masyarakat, seperti donor darah sukarela, bakti sosial peduli sesama, dan bantuan tanggap darurat saat bencana melanda.',
            ],
            [
                'sub_badge' => 'Tri Bakti 2',
                'icon' => 'fa-solid fa-kit-medical',
                'title' => 'Mempertinggi Keterampilan dan Memelihara Kebersihan dan Kesehatan',
                'description' => 'Mengasah keahlian Pertolongan Pertama (P3K), piket medis UKS, simulasi mitigasi bencana, serta membudayakan Pola Hidup Bersih dan Sehat (PHBS) di sekolah.',
            ],
            [
                'sub_badge' => 'Tri Bakti 3',
                'icon' => 'fa-solid fa-earth-asia',
                'title' => 'Mempererat Persahabatan Nasional dan Internasional',
                'description' => 'Menjalin persaudaraan relawan muda tanpa memandang perbedaan melalui temu akrab, JUMBARA (Jumpa Bakti Gembira), dan pertukaran informasi kepalangmerahan.',
            ],
        ];
    }

    public function getDisplayPrinciplesAttribute(): array
    {
        if (! empty($this->cards_data) && is_array($this->cards_data)) {
            $cleaned = [];
            foreach ($this->cards_data as $card) {
                if (! empty($card['title'])) {
                    $cleaned[] = [
                        'icon' => ! empty($card['icon']) ? $card['icon'] : 'fa-solid fa-heart',
                        'title' => $card['title'],
                        'subtitle' => $card['subtitle'] ?? '',
                    ];
                }
            }
            if (! empty($cleaned)) {
                return $cleaned;
            }
        }

        return [
            ['icon' => 'fa-solid fa-hand-holding-heart', 'title' => 'KEMANUSIAAN', 'subtitle' => 'Humanity'],
            ['icon' => 'fa-solid fa-scale-balanced', 'title' => 'KESAMAAN', 'subtitle' => 'Impartiality'],
            ['icon' => 'fa-solid fa-dove', 'title' => 'KENETRALAN', 'subtitle' => 'Neutrality'],
            ['icon' => 'fa-solid fa-flag', 'title' => 'KEMANDIRIAN', 'subtitle' => 'Independence'],
            ['icon' => 'fa-solid fa-heart', 'title' => 'KESUKARELAAN', 'subtitle' => 'Voluntary Service'],
            ['icon' => 'fa-solid fa-people-group', 'title' => 'KESATUAN', 'subtitle' => 'UNITY'],
            ['icon' => 'fa-solid fa-earth-americas', 'title' => 'KESEMESTAAN', 'subtitle' => 'Universality'],
        ];
    }

    public function getDisplayProgramsAttribute(): array
    {
        if (! empty($this->cards_data) && is_array($this->cards_data)) {
            $cleaned = [];
            foreach ($this->cards_data as $card) {
                if (! empty($card['title'])) {
                    $cleaned[] = [
                        'icon' => ! empty($card['icon']) ? $card['icon'] : 'fa-solid fa-droplet',
                        'title' => $card['title'],
                        'description' => $card['description'] ?? '',
                        'button_text' => ! empty($card['button_text']) ? $card['button_text'] : 'Lihat Selengkapnya',
                        'button_url' => ! empty($card['button_url']) ? $card['button_url'] : '#',
                    ];
                }
            }
            if (! empty($cleaned)) {
                return $cleaned;
            }
        }

        return [
            [
                'icon' => 'fa-solid fa-droplet',
                'title' => 'Donor Darah Sukarela',
                'description' => 'Edukasi pentingnya donor darah bagi remaja usia 17 tahun ke atas serta aksi donor darah berkala bekerja sama dengan PMI Kabupaten Bogor.',
                'button_text' => 'Cek Stok & Jadwal',
                'button_url' => route('donor-darah'),
            ],
            [
                'icon' => 'fa-solid fa-kit-medical',
                'title' => 'Pertolongan Pertama (PP)',
                'description' => 'Pelatihan intensif pembalutan, pembidaian, RJP/CPR, dan evakuasi korban untuk memastikan keselamatan di setiap kegiatan sekolah.',
                'button_text' => 'Lihat Modul & Kegiatan',
                'button_url' => route('kegiatan'),
            ],
            [
                'icon' => 'fa-solid fa-shield-halved',
                'title' => 'Siaga Bencana & Sosial',
                'description' => 'Simulasi evakuasi gempa, sekolah aman bencana, serta penggalangan donasi dan bakti sosial untuk warga terdampak musibah.',
                'button_text' => 'Panduan Mitigasi',
                'button_url' => route('artikel.index').'?kategori=kesiapsiagaan-bencana',
            ],
        ];
    }

    public function getDisplayChecklistItemsAttribute(): array
    {
        if (! empty($this->checklist_items) && is_array($this->checklist_items)) {
            $filtered = array_values(array_filter($this->checklist_items, fn ($item) => filled(trim((string) $item))));
            if (! empty($filtered)) {
                return $filtered;
            }
        }

        return [
            'Meningkatkan keterampilan pertolongan pertama (P3K) dan tanggap darurat bencana.',
            'Menyelenggarakan aksi donor darah sukarela secara berkala bersama UDD PMI.',
            'Membentuk jiwa kepemimpinan, kerelawanan, dan persahabatan antar siswa.',
        ];
    }

    public function getDisplayButtonTextAttribute(): string
    {
        return ! empty($this->button_text) ? $this->button_text : 'Baca Profil Selengkapnya';
    }

    public function getDisplayButtonUrlAttribute(): string
    {
        return ! empty($this->button_url) ? $this->button_url : route('tentang-kami');
    }

    public function getDisplayBadgeTextAttribute(): string
    {
        return ! empty($this->badge_text) ? $this->badge_text : 'Terakreditasi A';
    }

    public function getDisplayBadgeSubtextAttribute(): string
    {
        return ! empty($this->badge_subtext) ? $this->badge_subtext : 'Unit PMR Wira Teladan Kab. Bogor';
    }

    /**
     * URL gambar publik yang aman dan fleksibel.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('mockups/02_tentang_kami.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, '/')) {
            return asset(ltrim($this->image_path, '/'));
        }

        if (str_starts_with($this->image_path, 'storage/')) {
            return asset($this->image_path);
        }

        return asset('storage/'.ltrim($this->image_path, '/'));
    }

    /**
     * Dapatkan model section gambar dengan fallback aman.
     */
    public static function getSection(string $key): self
    {
        self::ensureTableExists();

        $section = self::where('section_key', $key)->first();
        if ($section) {
            return $section;
        }

        $defaults = [
            'home_profile' => [
                'section_key' => 'home_profile',
                'title' => 'Profil Ekstrakurikuler',
                'tagline' => 'Profil Ekstrakurikuler',
                'tagline_icon' => 'fa-solid fa-shield-heart',
                'headline' => 'Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi',
                'body_text' => 'Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA. Kami berkomitmen menanamkan nilai-nilai Tri Bakti PMR serta 7 Prinsip Dasar Palang Merah dan Bulan Sabit Merah Internasional.',
                'checklist_items' => [
                    'Meningkatkan keterampilan pertolongan pertama (P3K) dan tanggap darurat bencana.',
                    'Menyelenggarakan aksi donor darah sukarela secara berkala bersama UDD PMI.',
                    'Membentuk jiwa kepemimpinan, kerelawanan, dan persahabatan antar siswa.',
                ],
                'button_text' => 'Baca Profil Selengkapnya',
                'button_url' => '/tentang-kami',
                'page_name' => 'Halaman Beranda',
                'image_path' => '/mockups/02_tentang_kami.jpg',
                'badge_text' => 'Terakreditasi A',
                'badge_subtext' => 'Unit PMR Wira Teladan Kab. Bogor',
                'description' => 'Foto tim relawan di samping naskah profil Membangun Karakter Kemanusiaan.',
            ],
            'about_intro' => [
                'section_key' => 'about_intro',
                'title' => 'Mengenal PMR',
                'page_name' => 'Halaman Tentang Kami',
                'image_path' => '/mockups/02_tentang_kami.jpg',
                'tagline' => 'MENGENAL PMR',
                'tagline_icon' => 'fa-solid fa-book-open-reader',
                'headline' => 'Profil PMR Wira SMAN 1 Ciawi',
                'quote_text' => 'Palang Merah Remaja atau PMR adalah suatu organisasi binaan dari Palang Merah Indonesia yang berpusat di sekolah-sekolah ataupun kelompok-kelompok masyarakat (sanggar, kelompok belajar, dll.) yang bertujuan membangun dan mengembangkan karakter Kepalangmerahan agar siap menjadi Relawan PMI pada masa depan.',
                'body_text' => 'Di SMA Negeri 1 Ciawi, unit PMR Wira berperan aktif sebagai garda kemanusiaan di lingkungan sekolah, siaga memberikan pertolongan pertama, mempromosikan perilaku hidup bersih dan sehat, serta membina kepedulian sosial melalui aksi donor darah sukarela dan tanggap darurat bencana.',
                'body_text_secondary' => 'Masa bakti Ragana Dwi Pantara 2026/2027 berkomitmen mengamalkan seluruh nilai kepalangmerahan ini secara profesional, modern, dan penuh dedikasi bagi seluruh warga sekolah dan masyarakat sekitar.',
                'badge_text' => 'PMR Tingkat Wira',
                'badge_subtext' => 'Binaan PMI Kab. Bogor & SMAN 1 Ciawi',
                'badge_icon' => 'fa-solid fa-shield-halved',
                'description' => 'Foto profil pengenalan di samping naskah definisi resmi PMR Wira.',
            ],
            'about_tri_bakti' => [
                'section_key' => 'about_tri_bakti',
                'title' => 'Tri Bakti PMR',
                'page_name' => 'Halaman Tentang Kami',
                'image_path' => '',
                'tagline' => 'Tri Bakti PMR',
                'tagline_icon' => 'fa-solid fa-hands-holding-child',
                'headline' => 'Tri Bakti Palang Merah Remaja',
                'body_text' => 'Tiga pilar kewajiban utama yang senantiasa diamalkan oleh seluruh anggota PMR Wira SMAN 1 Ciawi dalam kehidupan sehari-hari:',
                'cards_data' => [
                    [
                        'sub_badge' => 'Tri Bakti 1',
                        'icon' => 'fa-solid fa-hand-holding-heart',
                        'title' => 'Berbakti kepada Masyarakat',
                        'description' => 'Turut serta dalam aksi kemanusiaan di masyarakat, seperti donor darah sukarela, bakti sosial peduli sesama, dan bantuan tanggap darurat saat bencana melanda.',
                    ],
                    [
                        'sub_badge' => 'Tri Bakti 2',
                        'icon' => 'fa-solid fa-kit-medical',
                        'title' => 'Mempertinggi Keterampilan dan Memelihara Kebersihan dan Kesehatan',
                        'description' => 'Mengasah keahlian Pertolongan Pertama (P3K), piket medis UKS, simulasi mitigasi bencana, serta membudayakan Pola Hidup Bersih dan Sehat (PHBS) di sekolah.',
                    ],
                    [
                        'sub_badge' => 'Tri Bakti 3',
                        'icon' => 'fa-solid fa-earth-asia',
                        'title' => 'Mempererat Persahabatan Nasional dan Internasional',
                        'description' => 'Menjalin persaudaraan relawan muda tanpa memandang perbedaan melalui temu akrab, JUMBARA (Jumpa Bakti Gembira), dan pertukaran informasi kepalangmerahan.',
                    ],
                ],
            ],
            'about_principles' => [
                'section_key' => 'about_principles',
                'title' => '7 Dasar Prinsip Palang Merah',
                'page_name' => 'Halaman Tentang Kami',
                'image_path' => '',
                'tagline' => 'Landasan Moral Universal',
                'headline' => '7 Dasar Prinsip Palang Merah',
                'body_text' => 'Tujuh prinsip fundamental yang menjadi pedoman teguh setiap langkah dan tindakan relawan PMR Wira di seluruh dunia:',
                'cards_data' => [
                    ['icon' => 'fa-solid fa-hand-holding-heart', 'title' => 'KEMANUSIAAN', 'subtitle' => 'Humanity'],
                    ['icon' => 'fa-solid fa-scale-balanced', 'title' => 'KESAMAAN', 'subtitle' => 'Impartiality'],
                    ['icon' => 'fa-solid fa-dove', 'title' => 'KENETRALAN', 'subtitle' => 'Neutrality'],
                    ['icon' => 'fa-solid fa-flag', 'title' => 'KEMANDIRIAN', 'subtitle' => 'Independence'],
                    ['icon' => 'fa-solid fa-heart', 'title' => 'KESUKARELAAN', 'subtitle' => 'Voluntary Service'],
                    ['icon' => 'fa-solid fa-people-group', 'title' => 'KESATUAN', 'subtitle' => 'UNITY'],
                    ['icon' => 'fa-solid fa-earth-americas', 'title' => 'KESEMESTAAN', 'subtitle' => 'Universality'],
                ],
            ],
            'about_vision_mission' => [
                'section_key' => 'about_vision_mission',
                'title' => 'Visi & Misi Organisasi',
                'page_name' => 'Halaman Tentang Kami',
                'image_path' => '',
                'tagline' => 'Arah Gerak Organisasi',
                'headline' => 'Visi & Misi Kami',
                'vision_text' => 'Terwujudnya PMR Wira SMAN 1 Ciawi sebagai unit relawan muda yang berkarakter kemanusiaan tangguh, sigap, berintegritas, mandiri, dan berprestasi berlandaskan prinsip-prinsip dasar Palang Merah Internasional.',
                'mission_items' => [
                    'Menanamkan pemahaman dan pengamalan 7 Prinsip Dasar Palang Merah dan Tri Bakti PMR.',
                    'Meningkatkan kapasitas anggota dalam keterampilan pertolongan pertama, sanitasi kesehatan, dan penanggulangan bencana.',
                    'Aktif menyelenggarakan program donor darah sukarela dan bakti sosial bagi masyarakat.',
                    'Menjalin sinergi erat dengan pembina sekolah, PMI Kabupaten Bogor, dan unit PMR lainnya.',
                ],
            ],
            'home_pillars' => [
                'section_key' => 'home_pillars',
                'title' => 'Program Unggulan PMR Wira',
                'page_name' => 'Halaman Beranda',
                'image_path' => '',
                'tagline' => 'Pilar Aksi Kemanusiaan',
                'headline' => 'Program Unggulan PMR Wira',
                'body_text' => 'Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.',
                'cards_data' => [
                    [
                        'icon' => 'fa-solid fa-droplet',
                        'title' => 'Donor Darah Sukarela',
                        'description' => 'Edukasi pentingnya donor darah bagi remaja usia 17 tahun ke atas serta aksi donor darah berkala bekerja sama dengan PMI Kabupaten Bogor.',
                        'button_text' => 'Cek Stok & Jadwal',
                        'button_url' => '/donor-darah',
                    ],
                    [
                        'icon' => 'fa-solid fa-kit-medical',
                        'title' => 'Pertolongan Pertama (PP)',
                        'description' => 'Pelatihan intensif pembalutan, pembidaian, RJP/CPR, dan evakuasi korban untuk memastikan keselamatan di setiap kegiatan sekolah.',
                        'button_text' => 'Lihat Modul & Kegiatan',
                        'button_url' => '/kegiatan',
                    ],
                    [
                        'icon' => 'fa-solid fa-shield-halved',
                        'title' => 'Siaga Bencana & Sosial',
                        'description' => 'Simulasi evakuasi gempa, sekolah aman bencana, serta penggalangan donasi dan bakti sosial untuk warga terdampak musibah.',
                        'button_text' => 'Panduan Mitigasi',
                        'button_url' => '/artikel?kategori=kesiapsiagaan-bencana',
                    ],
                ],
            ],
        ];

        $data = $defaults[$key] ?? [
            'section_key' => $key,
            'title' => ucfirst(str_replace('_', ' ', $key)),
            'page_name' => 'Website',
            'image_path' => '/mockups/02_tentang_kami.jpg',
        ];

        return self::create($data);
    }

    /**
     * Pastikan tabel content_section_images siap digunakan.
     */
    public static function ensureTableExists(): void
    {
        if (! Schema::hasTable('content_section_images')) {
            Schema::create('content_section_images', function ($table) {
                $table->id();
                $table->string('section_key')->unique();
                $table->string('title');
                $table->string('page_name');
                $table->string('image_path');
                $table->string('badge_text')->nullable();
                $table->string('badge_subtext')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }
}
