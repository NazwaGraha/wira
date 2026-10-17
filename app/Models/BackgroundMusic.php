<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BackgroundMusic extends Model
{
    use HasFactory;

    protected $table = 'background_musics';

    protected $fillable = [
        'title',
        'subtitle',
        'audio_path',
        'is_active',
        'autoplay_on_first_interaction',
        'default_volume',
        'loop',
        'lyrics',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'autoplay_on_first_interaction' => 'boolean',
        'loop' => 'boolean',
        'default_volume' => 'integer',
    ];

    /**
     * URL audio yang siap diputar di browser.
     */
    public function getAudioUrlAttribute(): string
    {
        try {
            return route('mars-pmi.stream');
        } catch (\Throwable $e) {
            if (empty($this->audio_path)) {
                if (file_exists(public_path('audio/mars-pmi.mp3'))) {
                    return asset('audio/mars-pmi.mp3');
                }

                return asset('audio/mars-pmi.m4a');
            }

            if (str_starts_with($this->audio_path, 'http://') || str_starts_with($this->audio_path, 'https://')) {
                return $this->audio_path;
            }

            if (str_starts_with($this->audio_path, '/')) {
                return asset(ltrim($this->audio_path, '/'));
            }

            return Storage::url($this->audio_path);
        }
    }
}
