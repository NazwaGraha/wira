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
    ];

    /**
     * Fallback getters untuk memastikan tampilan visual 100% konsisten dengan desain beranda.
     */
    public function getDisplayTaglineAttribute(): string
    {
        return ! empty($this->tagline) ? $this->tagline : 'Profil Ekstrakurikuler';
    }

    public function getDisplayTaglineIconAttribute(): string
    {
        return ! empty($this->tagline_icon) ? $this->tagline_icon : 'fa-solid fa-shield-heart';
    }

    public function getDisplayHeadlineAttribute(): string
    {
        return ! empty($this->headline) ? $this->headline : 'Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi';
    }

    public function getDisplayBodyTextAttribute(): string
    {
        return ! empty($this->body_text) ? $this->body_text : 'Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA. Kami berkomitmen menanamkan nilai-nilai Tri Bakti PMR serta 7 Prinsip Dasar Palang Merah dan Bulan Sabit Merah Internasional.';
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
