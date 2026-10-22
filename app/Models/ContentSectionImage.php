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
        'page_name',
        'image_path',
        'badge_text',
        'badge_subtext',
        'description',
    ];

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
