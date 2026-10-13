<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Relasi ke media galeri (foto & video)
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'gallery_category_id');
    }

    /**
     * Hitung total foto dalam kategori ini
     */
    public function photosCount()
    {
        return $this->galleries()->where('type', 'photo')->count();
    }

    /**
     * Hitung total video dalam kategori ini
     */
    public function videosCount()
    {
        return $this->galleries()->where('type', 'video')->count();
    }
}
