<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionInfoMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_event_id',
        'title',
        'category_badge',
        'icon',
        'color_theme',
        'description',
        'action_type',
        'file_path',
        'url_link',
        'contacts_data',
        'button_text',
        'order_position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_position' => 'integer',
        'contacts_data' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompetitionEvent::class, 'competition_event_id');
    }

    /**
     * Get the 3 contact persons (PMR Mula, PMR Madya, PMR Wira)
     */
    public function getContactsListAttribute(): array
    {
        $raw = (!empty($this->contacts_data) && is_array($this->contacts_data) && count($this->contacts_data) > 0)
            ? $this->contacts_data
            : self::defaultContacts();

        $levelMeta = [
            'Mula' => [
                'label' => 'PMR Mula (SD / MI)',
                'color' => 'emerald',
                'badge_bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'dot_bg' => 'bg-emerald-500',
                'role_default' => 'Koordinator PMR Mula (SD/MI)',
            ],
            'Madya' => [
                'label' => 'PMR Madya (SMP / MTs)',
                'color' => 'blue',
                'badge_bg' => 'bg-blue-100 text-blue-800 border-blue-300',
                'dot_bg' => 'bg-blue-500',
                'role_default' => 'Koordinator PMR Madya (SMP/MTs)',
            ],
            'Wira' => [
                'label' => 'PMR Wira (SMA / SMK / MA)',
                'color' => 'amber',
                'badge_bg' => 'bg-amber-100 text-amber-800 border-amber-300',
                'dot_bg' => 'bg-amber-500',
                'role_default' => 'Koordinator PMR Wira (SMA/SMK/MA)',
            ],
        ];

        $enriched = [];
        foreach ($raw as $item) {
            $lvl = $item['level'] ?? 'Lomba';
            $meta = $levelMeta[$lvl] ?? [
                'label' => "PMR {$lvl}",
                'color' => 'slate',
                'badge_bg' => 'bg-slate-100 text-slate-800 border-slate-200',
                'dot_bg' => 'bg-slate-500',
                'role_default' => "Koordinator PMR {$lvl}",
            ];

            $name = $item['name'] ?? "Kak Panitia {$lvl}";
            $phone = $item['phone'] ?? '081383885600';
            $role = !empty($item['role']) ? $item['role'] : $meta['role_default'];

            $enriched[] = [
                'level' => $lvl,
                'label' => $item['label'] ?? $meta['label'],
                'name' => $name,
                'phone' => $phone,
                'role' => $role,
                'color' => $item['color'] ?? $meta['color'],
                'badge_bg' => $meta['badge_bg'],
                'dot_bg' => $meta['dot_bg'],
                'wa_url' => self::formatWhatsAppUrl($phone, $lvl, $name),
            ];
        }

        return $enriched;
    }

    /**
     * Default 3 contact persons
     */
    public static function defaultContacts(): array
    {
        return [
            [
                'level' => 'Mula',
                'label' => 'PMR Mula (SD / MI)',
                'name' => 'Kak Panitia Mula',
                'phone' => '081383885600',
                'role' => 'Koordinator PMR Mula (SD/MI)',
                'color' => 'emerald',
            ],
            [
                'level' => 'Madya',
                'label' => 'PMR Madya (SMP / MTs)',
                'name' => 'Kak Panitia Madya',
                'phone' => '081383885600',
                'role' => 'Koordinator PMR Madya (SMP/MTs)',
                'color' => 'blue',
            ],
            [
                'level' => 'Wira',
                'label' => 'PMR Wira (SMA / SMK / MA)',
                'name' => 'Kak Panitia Wira',
                'phone' => '081383885600',
                'role' => 'Koordinator PMR Wira (SMA/SMK/MA)',
                'color' => 'amber',
            ],
        ];
    }

    /**
     * Format WhatsApp chat URL for a contact person
     */
    public static function formatWhatsAppUrl(?string $phone, string $level = 'Lomba', string $name = ''): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (!empty($cleanPhone) && !str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        if (empty($cleanPhone)) {
            $cleanPhone = '6281383885600';
        }

        $greeting = !empty($name) ? "Halo Kak {$name}" : "Halo Panitia";
        $message = "{$greeting}, saya ingin bertanya seputar informasi lomba untuk PMR Tingkat {$level} di Sua Bhakti Berkarya.";
        return "https://wa.me/{$cleanPhone}?text=" . rawurlencode($message);
    }

    /**
     * Resolves the actual target URL depending on action_type
     */
    public function getTargetUrlAttribute(): ?string
    {
        if ($this->action_type === 'file' && $this->file_path) {
            return asset('storage/' . $this->file_path);
        }

        if ($this->action_type === 'link' || $this->action_type === 'whatsapp') {
            return $this->url_link;
        }

        return null;
    }

    /**
     * Get theme color classes dictionary
     */
    public function getThemeClassesAttribute(): array
    {
        $theme = strtolower($this->color_theme ?: 'red');

        $palettes = [
            'red' => [
                'border_hover' => 'hover:border-red-500/60',
                'shadow_hover' => 'hover:shadow-red-950/30',
                'icon_bg' => 'bg-red-500/15',
                'icon_hover' => 'group-hover:bg-red-500/25',
                'icon_text' => 'text-red-400',
                'badge_bg' => 'bg-red-950/80',
                'badge_text' => 'text-red-300',
                'badge_border' => 'border-red-500/30',
                'title_hover' => 'group-hover:text-red-300',
                'btn_bg' => 'bg-red-600/20 hover:bg-red-600',
                'btn_text' => 'text-red-300 hover:text-white',
                'btn_border' => 'border-red-500/30',
                'btn_solid' => 'bg-red-600 hover:bg-red-500 text-white shadow-red-900/40',
            ],
            'sky' => [
                'border_hover' => 'hover:border-sky-500/60',
                'shadow_hover' => 'hover:shadow-sky-950/30',
                'icon_bg' => 'bg-sky-500/15',
                'icon_hover' => 'group-hover:bg-sky-500/25',
                'icon_text' => 'text-sky-400',
                'badge_bg' => 'bg-sky-950/80',
                'badge_text' => 'text-sky-300',
                'badge_border' => 'border-sky-500/30',
                'title_hover' => 'group-hover:text-sky-300',
                'btn_bg' => 'bg-sky-600/20 hover:bg-sky-600',
                'btn_text' => 'text-sky-300 hover:text-white',
                'btn_border' => 'border-sky-500/30',
                'btn_solid' => 'bg-sky-600 hover:bg-sky-500 text-white shadow-sky-900/40',
            ],
            'rose' => [
                'border_hover' => 'hover:border-rose-500/60',
                'shadow_hover' => 'hover:shadow-rose-950/30',
                'icon_bg' => 'bg-rose-500/15',
                'icon_hover' => 'group-hover:bg-rose-500/25',
                'icon_text' => 'text-rose-400',
                'badge_bg' => 'bg-rose-950/80',
                'badge_text' => 'text-rose-300',
                'badge_border' => 'border-rose-500/30',
                'title_hover' => 'group-hover:text-rose-300',
                'btn_bg' => 'bg-rose-600/20 hover:bg-rose-600',
                'btn_text' => 'text-rose-300 hover:text-white',
                'btn_border' => 'border-rose-500/30',
                'btn_solid' => 'bg-rose-600 hover:bg-rose-500 text-white shadow-rose-900/40',
            ],
            'amber' => [
                'border_hover' => 'hover:border-amber-500/60',
                'shadow_hover' => 'hover:shadow-amber-950/30',
                'icon_bg' => 'bg-amber-500/15',
                'icon_hover' => 'group-hover:bg-amber-500/25',
                'icon_text' => 'text-amber-400',
                'badge_bg' => 'bg-amber-950/80',
                'badge_text' => 'text-amber-300',
                'badge_border' => 'border-amber-500/30',
                'title_hover' => 'group-hover:text-amber-300',
                'btn_bg' => 'bg-amber-600/20 hover:bg-amber-600',
                'btn_text' => 'text-amber-300 hover:text-white',
                'btn_border' => 'border-amber-500/30',
                'btn_solid' => 'bg-amber-600 hover:bg-amber-500 text-white shadow-amber-900/40',
            ],
            'emerald' => [
                'border_hover' => 'hover:border-emerald-500/60',
                'shadow_hover' => 'hover:shadow-emerald-950/30',
                'icon_bg' => 'bg-emerald-500/15',
                'icon_hover' => 'group-hover:bg-emerald-500/25',
                'icon_text' => 'text-emerald-400',
                'badge_bg' => 'bg-emerald-950/80',
                'badge_text' => 'text-emerald-300',
                'badge_border' => 'border-emerald-500/30',
                'title_hover' => 'group-hover:text-emerald-300',
                'btn_bg' => 'bg-emerald-600/20 hover:bg-emerald-600',
                'btn_text' => 'text-emerald-300 hover:text-white',
                'btn_border' => 'border-emerald-500/30',
                'btn_solid' => 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-950/40',
            ],
            'purple' => [
                'border_hover' => 'hover:border-purple-500/60',
                'shadow_hover' => 'hover:shadow-purple-950/30',
                'icon_bg' => 'bg-purple-500/15',
                'icon_hover' => 'group-hover:bg-purple-500/25',
                'icon_text' => 'text-purple-400',
                'badge_bg' => 'bg-purple-950/80',
                'badge_text' => 'text-purple-300',
                'badge_border' => 'border-purple-500/30',
                'title_hover' => 'group-hover:text-purple-300',
                'btn_bg' => 'bg-purple-600/20 hover:bg-purple-600',
                'btn_text' => 'text-purple-300 hover:text-white',
                'btn_border' => 'border-purple-500/30',
                'btn_solid' => 'bg-purple-600 hover:bg-purple-500 text-white shadow-purple-900/40',
            ],
            'cyan' => [
                'border_hover' => 'hover:border-cyan-500/60',
                'shadow_hover' => 'hover:shadow-cyan-950/30',
                'icon_bg' => 'bg-cyan-500/15',
                'icon_hover' => 'group-hover:bg-cyan-500/25',
                'icon_text' => 'text-cyan-400',
                'badge_bg' => 'bg-cyan-950/80',
                'badge_text' => 'text-cyan-300',
                'badge_border' => 'border-cyan-500/30',
                'title_hover' => 'group-hover:text-cyan-300',
                'btn_bg' => 'bg-cyan-600/20 hover:bg-cyan-600',
                'btn_text' => 'text-cyan-300 hover:text-white',
                'btn_border' => 'border-cyan-500/30',
                'btn_solid' => 'bg-cyan-600 hover:bg-cyan-500 text-white shadow-cyan-900/40',
            ],
            'indigo' => [
                'border_hover' => 'hover:border-indigo-500/60',
                'shadow_hover' => 'hover:shadow-indigo-950/30',
                'icon_bg' => 'bg-indigo-500/15',
                'icon_hover' => 'group-hover:bg-indigo-500/25',
                'icon_text' => 'text-indigo-400',
                'badge_bg' => 'bg-indigo-950/80',
                'badge_text' => 'text-indigo-300',
                'badge_border' => 'border-indigo-500/30',
                'title_hover' => 'group-hover:text-indigo-300',
                'btn_bg' => 'bg-indigo-600/20 hover:bg-indigo-600',
                'btn_text' => 'text-indigo-300 hover:text-white',
                'btn_border' => 'border-indigo-500/30',
                'btn_solid' => 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-900/40',
            ],
        ];

        return $palettes[$theme] ?? $palettes['red'];
    }

    /**
     * Default list of the 8 information menu items
     */
    public static function defaultItems(): array
    {
        return [
            [
                'title' => 'Surat Rekomendasi',
                'category_badge' => 'Dokumen Resmi',
                'icon' => 'fa-solid fa-file-shield',
                'color_theme' => 'red',
                'description' => 'Dokumen rekomendasi izin kegiatan dari PMI dan instansi kedinasan terkait.',
                'action_type' => 'notice',
                'file_path' => null,
                'url_link' => null,
                'button_text' => 'Unduh Dokumen',
                'order_position' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Surat Undangan Lomba',
                'category_badge' => 'Undangan',
                'icon' => 'fa-solid fa-envelope-open-text',
                'color_theme' => 'sky',
                'description' => 'Surat edaran undangan resmi partisipasi lomba untuk kepala sekolah & pembina PMR.',
                'action_type' => 'notice',
                'file_path' => null,
                'url_link' => null,
                'button_text' => 'Unduh Undangan',
                'order_position' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Juklak Juknis',
                'category_badge' => 'Wajib Dibaca',
                'icon' => 'fa-solid fa-book-bookmark',
                'color_theme' => 'rose',
                'description' => 'Petunjuk Pelaksanaan & Petunjuk Teknis aturan resmi perlombaan dan tata tertib.',
                'action_type' => 'notice',
                'file_path' => null,
                'url_link' => null,
                'button_text' => 'Unduh Juklak Juknis',
                'order_position' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Grid Nilai',
                'category_badge' => 'Transparansi',
                'icon' => 'fa-solid fa-table-list',
                'color_theme' => 'amber',
                'description' => 'Matriks rubrik penilaian juri, bobot kriteria teknis, dan rumus perhitungan skor.',
                'action_type' => 'notice',
                'file_path' => null,
                'url_link' => null,
                'button_text' => 'Lihat Rubrik Nilai',
                'order_position' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Peta Lokasi Lomba',
                'category_badge' => 'Venue & Denah',
                'icon' => 'fa-solid fa-map-location-dot',
                'color_theme' => 'emerald',
                'description' => 'Denah kampus SMAN 1 Ciawi, posisi pos mata lomba, area transit, dan rute navigasi.',
                'action_type' => 'link',
                'file_path' => null,
                'url_link' => 'https://maps.google.com/?q=SMAN+1+Ciawi+Bogor',
                'button_text' => 'Buka Google Maps',
                'order_position' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Buku Panduan Lomba',
                'category_badge' => 'Handbook',
                'icon' => 'fa-solid fa-book-open-reader',
                'color_theme' => 'purple',
                'description' => 'Panduan teknis operasional untuk kontingen, pembina pendamping, dan peserta lomba.',
                'action_type' => 'notice',
                'file_path' => null,
                'url_link' => null,
                'button_text' => 'Unduh Handbook',
                'order_position' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Dokumentasi Kegiatan',
                'category_badge' => 'Galeri Media',
                'icon' => 'fa-solid fa-photo-film',
                'color_theme' => 'cyan',
                'description' => 'Koleksi foto, video rekaman lomba, dan kilas balik gelaran Sua Bhakti Berkarya.',
                'action_type' => 'link',
                'file_path' => null,
                'url_link' => 'https://instagram.com/pmrwirasman1c',
                'button_text' => 'Buka Galeri Foto & Video',
                'order_position' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Contact Person',
                'category_badge' => 'Hotline 24/7',
                'icon' => 'fa-brands fa-whatsapp',
                'color_theme' => 'emerald',
                'description' => 'Layanan konsultasi narahubung panitia untuk PMR Mula, PMR Madya, dan PMR Wira.',
                'action_type' => 'whatsapp',
                'file_path' => null,
                'url_link' => 'https://wa.me/6281383885600',
                'contacts_data' => self::defaultContacts(),
                'button_text' => 'Hubungi Panitia',
                'order_position' => 8,
                'is_active' => true,
            ],
        ];
    }
}
