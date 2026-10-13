<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'type',
        'title',
        'category',
        'gallery_category_id',
        'image_path',
        'video_url',
        'duration',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke GalleryCategory
     */
    public function galleryCategory()
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }

    /**
     * Accessor nama kategori (prioritaskan nama dari relasi jika ada, atau fallback ke category string)
     */
    public function getCategoryNameAttribute()
    {
        return $this->galleryCategory->name ?? $this->category ?? '-';
    }
}
