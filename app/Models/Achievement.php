<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'event_name',
        'organizer',
        'event_date',
        'level',
        'rank',
        'category',
        'contingent_members',
        'description',
        'trophy_image',
        'gallery_images',
        'certificate_file',
        'is_featured',
        'is_active',
        'order_position',
    ];

    protected $casts = [
        'event_date' => 'date',
        'contingent_members' => 'array',
        'gallery_images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'order_position' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Resolusi URL foto utama piala / penghargaan
     */
    public function getTrophyImageUrlAttribute(): string
    {
        if (! empty($this->trophy_image)) {
            if (str_starts_with($this->trophy_image, 'http://') || str_starts_with($this->trophy_image, 'https://')) {
                return $this->trophy_image;
            }

            return asset('storage/'.$this->trophy_image);
        }

        return 'https://images.unsplash.com/photo-1578269174936-2709b6aeb913?auto=format&fit=crop&w=600&q=80';
    }

    /**
     * Resolusi URL koleksi foto galeri serah terima penghargaan
     */
    public function getGalleryUrlsAttribute(): array
    {
        $images = is_array($this->gallery_images) ? $this->gallery_images : [];
        $urls = [];

        foreach ($images as $img) {
            if (empty($img)) {
                continue;
            }
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                $urls[] = $img;
            } else {
                $urls[] = asset('storage/'.$img);
            }
        }

        return $urls;
    }

    /**
     * Resolusi URL dokumen sertifikat/piagam
     */
    public function getCertificateUrlAttribute(): ?string
    {
        if (empty($this->certificate_file)) {
            return null;
        }

        if (str_starts_with($this->certificate_file, 'http://') || str_starts_with($this->certificate_file, 'https://')) {
            return $this->certificate_file;
        }

        return asset('storage/'.$this->certificate_file);
    }

    /**
     * Badge visual class berdasarkan predikat juara
     */
    public function getRankBadgeClassAttribute(): string
    {
        $r = strtolower($this->rank);
        if (str_contains($r, 'umum')) {
            return 'bg-gradient-to-r from-amber-500 to-yellow-400 text-stone-950 border border-yellow-300 shadow-md';
        }
        if (str_contains($r, '1')) {
            return 'bg-gradient-to-r from-red-600 to-rose-500 text-white border border-red-400 shadow-sm';
        }
        if (str_contains($r, '2')) {
            return 'bg-gradient-to-r from-slate-600 to-slate-500 text-white border border-slate-400 shadow-sm';
        }
        if (str_contains($r, '3')) {
            return 'bg-gradient-to-r from-amber-700 to-yellow-800 text-amber-100 border border-amber-600 shadow-sm';
        }

        return 'bg-pmr-primary text-white border border-red-400';
    }
}
