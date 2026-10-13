<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'thumbnail',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyword',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'category_id',
        'user_id',
        'author_name',
        'status',
        'is_featured',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (empty($this->thumbnail)) {
            return asset('mockups/07_artikel.jpg');
        }

        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://') || str_starts_with($this->thumbnail, '/')) {
            return $this->thumbnail;
        }

        return asset('storage/'.$this->thumbnail);
    }

    public function getEffectiveMetaTitleAttribute(): string
    {
        return $this->meta_title ?: ($this->title.' - PMR Wira SMAN 1 Ciawi');
    }

    public function getEffectiveMetaDescriptionAttribute(): string
    {
        if (! empty($this->meta_description)) {
            return $this->meta_description;
        }

        if (! empty($this->excerpt)) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags($this->body), 160);
    }

    public function getEffectiveOgImageAttribute(): string
    {
        if (! empty($this->og_image)) {
            if (str_starts_with($this->og_image, 'http://') || str_starts_with($this->og_image, 'https://') || str_starts_with($this->og_image, '/')) {
                return $this->og_image;
            }

            return asset('storage/'.$this->og_image);
        }

        return $this->thumbnail_url;
    }

    public function getEstimatedReadingTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->body));
        $minutes = ceil($words / 200);

        return max(1, $minutes).' min baca';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title).'-'.Str::random(5);
            }
            if ($article->status === 'published' && empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }
}
