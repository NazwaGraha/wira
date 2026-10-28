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
        'body_text',
        'checklist_items',
        'cards_data',
        'button_text',
        'button_url',
        'page_name',
        'image_path',
        'badge_text',
        'badge_subtext',
        'description',
    ];

    protected $casts = [
        'checklist_items' => 'array',
        'cards_data' => 'array',
    ];

    /**
     * Fallback getters untuk memastikan tampilan visual 100% konsisten dengan desain beranda.
     */
    public function getDisplayTaglineAttribute(): string
    {
        if (! empty($this->tagline)) {
            return $this->tagline;
        }

        return $this->section_key === 'home_pillars' ? 'Pilar Aksi Kemanusiaan' : 'Profil Ekstrakurikuler';
    }

    public function getDisplayTaglineIconAttribute(): string
    {
        return ! empty($this->tagline_icon) ? $this->tagline_icon : 'fa-solid fa-shield-heart';
    }

    public function getDisplayHeadlineAttribute(): string
    {
        if (! empty($this->headline)) {
            return $this->headline;
        }

        return $this->section_key === 'home_pillars' ? 'Program Unggulan PMR Wira' : 'Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi';
    }

    public function getDisplayBodyTextAttribute(): string
    {
        if (! empty($this->body_text)) {
            return $this->body_text;
        }

        return $this->section_key === 'home_pillars'
            ? 'Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.'
            : 'Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA. Kami berkomitmen menanamkan nilai-nilai Tri Bakti PMR serta 7 Prinsip Dasar Palang Merah dan Bulan Sabit Merah Internasional.';
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
                'badge_text' => 'PMR Tingkat Wira',
                'badge_subtext' => 'Binaan PMI Kab. Bogor & SMAN 1 Ciawi',
                'description' => 'Foto profil pengenalan di samping naskah definisi resmi PMR Wira.',
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
