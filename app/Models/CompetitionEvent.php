<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'theme',
        'start_date',
        'end_date',
        'location',
        'description',
        'banner_image',
        'handbook_file',
        'registration_fee',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'is_active',
        'is_registration_open',
        'wave1_name',
        'wave1_start_date',
        'wave1_end_date',
        'wave2_name',
        'wave2_start_date',
        'wave2_end_date',
        'is_waves_enabled',
        'display_mode',
    ];

    /**
     * Get the display state for competition landing page:
     * - 'empty': no active event or off-season
     * - 'teaser': upcoming event, still months away or forced teaser
     * - 'active': active event with registration open / short-term countdown
     */
    public function getDisplayState(): string
    {
        if (! $this->is_active) {
            return 'empty';
        }

        $mode = $this->display_mode ?? 'auto';

        if ($mode === 'teaser') {
            return 'teaser';
        }

        if ($mode === 'active') {
            return 'active';
        }

        // Auto mode logic
        // If registration is currently open, it is definitely active
        if ($this->is_registration_open) {
            return 'active';
        }

        $now = now();

        // If wave 1 start date is in the future (> 7 days), show teaser
        if ($this->wave1_start_date && $now->lt($this->wave1_start_date)) {
            if ($now->diffInDays($this->wave1_start_date, false) >= 7) {
                return 'teaser';
            }

            return 'active';
        }

        // If event start date is in the future (> 14 days) and registration is closed
        if ($this->start_date && $now->lt($this->start_date)) {
            if ($now->diffInDays($this->start_date, false) >= 14) {
                return 'teaser';
            }
        }

        return 'active';
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'wave1_start_date' => 'datetime',
        'wave1_end_date' => 'datetime',
        'wave2_start_date' => 'datetime',
        'wave2_end_date' => 'datetime',
        'is_waves_enabled' => 'boolean',
        'registration_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'is_registration_open' => 'boolean',
    ];

    /**
     * Get the currently active wave status.
     * Returns:
     * - 'upcoming': before wave 1 starts
     * - 1: Wave 1 is active
     * - 2: Wave 2 is active
     * - 'closed': after wave 2 ends or registration closed
     */
    public function getActiveWave(): string|int
    {
        if (! $this->is_registration_open) {
            return 'closed';
        }

        if (! $this->is_waves_enabled) {
            return 1;
        }

        $now = now();

        // If wave 1 start date is set and now is before wave 1 start
        if ($this->wave1_start_date && $now->lt($this->wave1_start_date)) {
            return 'upcoming';
        }

        // If wave 1 is ongoing
        if ($this->wave1_start_date && $this->wave1_end_date) {
            if ($now->gte($this->wave1_start_date) && $now->lte($this->wave1_end_date)) {
                return 1;
            }
        } elseif ($this->wave1_end_date && $now->lte($this->wave1_end_date)) {
            return 1;
        }

        // If wave 2 is ongoing
        if ($this->wave2_start_date && $this->wave2_end_date) {
            if ($now->gte($this->wave2_start_date) && $now->lte($this->wave2_end_date)) {
                return 2;
            }
        } elseif ($this->wave2_end_date && $now->lte($this->wave2_end_date)) {
            return 2;
        }

        // If wave 2 has passed
        if ($this->wave2_end_date && $now->gt($this->wave2_end_date)) {
            return 'closed';
        }

        // Default fallback
        return 1;
    }

    /**
     * Get the target datetime for countdown timer.
     */
    public function getCountdownTarget(): ?Carbon
    {
        if ($this->getDisplayState() === 'teaser') {
            return $this->wave1_start_date ?? ($this->start_date ? $this->start_date->copy()->startOfDay() : null);
        }

        $activeWave = $this->getActiveWave();

        if ($activeWave === 'upcoming') {
            return $this->wave1_start_date;
        }

        if ($activeWave === 1) {
            return $this->wave1_end_date;
        }

        if ($activeWave === 2) {
            return $this->wave2_end_date;
        }

        return null;
    }

    /**
     * Get human-friendly label for current active wave.
     */
    public function getActiveWaveLabel(): string
    {
        $wave = $this->getActiveWave();
        if ($wave === 'upcoming') {
            return 'Segera Dibuka: '.($this->wave1_name ?: 'Gelombang 1');
        }
        if ($wave === 1) {
            return $this->wave1_name ?: 'Gelombang 1';
        }
        if ($wave === 2) {
            return $this->wave2_name ?: 'Gelombang 2';
        }

        return 'Pendaftaran Ditutup';
    }

    public function categories(): HasMany
    {
        return $this->hasMany(CompetitionCategory::class)->orderBy('order_position');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function infoMenus(): HasMany
    {
        return $this->hasMany(CompetitionInfoMenu::class)->orderBy('order_position');
    }
}
