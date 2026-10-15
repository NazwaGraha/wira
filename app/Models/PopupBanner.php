<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PopupBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'target_url',
        'button_text',
        'open_in_new_tab',
        'start_date',
        'end_date',
        'is_active',
        'impressions_count',
        'clicks_count',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    /**
     * Scope to retrieve banner currently eligible for display.
     */
    public function scopeCurrentlyActive($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });
    }

    /**
     * Image URL accessor.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/logo.png');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }

    /**
     * Computed Status Badge.
     */
    public function getStatusLabelAttribute(): array
    {
        if (! $this->is_active) {
            return [
                'text' => 'Nonaktif',
                'class' => 'bg-slate-100 text-slate-600 border border-slate-300',
                'icon' => 'fa-solid fa-circle-pause',
            ];
        }

        $now = now();

        if ($this->start_date && $this->start_date->isFuture()) {
            return [
                'text' => 'Terjadwal',
                'class' => 'bg-blue-50 text-blue-700 border border-blue-200',
                'icon' => 'fa-solid fa-clock',
            ];
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return [
                'text' => 'Kadaluarsa',
                'class' => 'bg-amber-50 text-amber-700 border border-amber-200',
                'icon' => 'fa-solid fa-calendar-xmark',
            ];
        }

        return [
            'text' => 'Aktif Tayang',
            'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'icon' => 'fa-solid fa-circle-check',
        ];
    }

    /**
     * CTR (Click-Through Rate) percentage.
     */
    public function getCtrAttribute(): float
    {
        if ($this->impressions_count <= 0) {
            return 0.0;
        }

        return round(($this->clicks_count / $this->impressions_count) * 100, 1);
    }
}
