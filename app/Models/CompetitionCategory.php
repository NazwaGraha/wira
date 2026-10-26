<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_event_id',
        'code',
        'name',
        'level',
        'gender_category',
        'scoring_type',
        'point_tier',
        'criteria_schema',
        'registration_fee',
        'registration_fee_wave1',
        'registration_fee_wave2',
        'max_team_members',
        'has_rounds',
        'order_position',
        'is_active',
    ];

    protected $casts = [
        'criteria_schema' => 'array',
        'registration_fee' => 'decimal:2',
        'registration_fee_wave1' => 'decimal:2',
        'registration_fee_wave2' => 'decimal:2',
        'max_team_members' => 'integer',
        'has_rounds' => 'boolean',
        'order_position' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get fee for a specific wave with fallback.
     */
    public function getFeeForWave(int|string $wave = 1): float
    {
        if ($wave == 2) {
            return floatval($this->registration_fee_wave2 !== null ? $this->registration_fee_wave2 : ($this->registration_fee ?? 150000));
        }

        return floatval($this->registration_fee_wave1 !== null ? $this->registration_fee_wave1 : ($this->registration_fee ?? 150000));
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompetitionEvent::class, 'competition_event_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(CompetitionParticipantTeam::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionScore::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $gender = ($this->gender_category && $this->gender_category !== 'Umum') ? " {$this->gender_category}" : '';

        return "{$this->name} - {$this->level}{$gender}";
    }

    public function getRegistrationFeeAttribute($value)
    {
        return $value !== null ? $value : 150000;
    }

    /**
     * Cek apakah cabang lomba memiliki kuota regu tak terbatas (khusus Mewarnai).
     */
    public function isUnlimitedTeams(): bool
    {
        $name = strtolower($this->name ?? '');

        return str_contains($name, 'mewarnai');
    }

    /**
     * Cek apakah cabang lomba memperbolehkan pendaftaran lebih dari 1 regu.
     * Semua cabang lomba kini mengizinkan multi regu (2 regu untuk cabang umum, 3 regu untuk olimpiade, tak terbatas untuk mewarnai).
     */
    public function isMultiTeamAllowed(): bool
    {
        return true;
    }

    /**
     * Dapatkan jumlah standar anggota dalam satu regu untuk cabang lomba ini:
     * - Pertolongan pertama : 3 orang dalam satu regu
     * - Ketangkasan tandu : 2 orang dalam satu regu
     * - Ketangkasan cuci tangan : 2 orang dalam satu regu
     * - Mewarnai : 1 orang dalam satu regu
     * - Cepat tepat : 3 orang dalam satu regu
     * - Pendidikan remaja sebaya / PMR Favorite : 2 orang dalam satu regu
     * - Olimpiade kepalangmerahan : 1 orang dalam satu regu
     */
    public function getRequiredTeamMembers(): int
    {
        if ($this->max_team_members && $this->max_team_members > 0) {
            return (int) $this->max_team_members;
        }

        $name = strtolower($this->name ?? '');

        if (str_contains($name, 'pertolongan pertama')) {
            return 3;
        }
        if (str_contains($name, 'tandu')) {
            return 2;
        }
        if (str_contains($name, 'cuci tangan')) {
            return 2;
        }
        if (str_contains($name, 'mewarnai')) {
            return 1;
        }
        if (str_contains($name, 'cepat tepat')) {
            return 3;
        }
        if (str_contains($name, 'pendidikan remaja sebaya') || str_contains($name, 'prs') || str_contains($name, 'favorite') || str_contains($name, 'favorit')) {
            return 2;
        }
        if (str_contains($name, 'olimpiade') || str_contains($name, 'olympiade')) {
            return 1;
        }
        if (str_contains($name, 'mading')) {
            return 2;
        }

        return 2;
    }

    /**
     * Dapatkan kuota maksimum regu per sekolah untuk cabang lomba ini:
     * - Mewarnai: Tidak dibatasi (unlimited).
     * - Olimpiade Kepalangmerahan: Maksimal 3 regu.
     * - Cabang lomba umum: Maksimal 2 regu (2 regu putra / 2 regu putri).
     */
    public function maxTeamsPerSchool(): int
    {
        if ($this->isUnlimitedTeams()) {
            return 999;
        }

        $name = strtolower($this->name ?? '');
        if (str_contains($name, 'olimpiade') || str_contains($name, 'olympiade')) {
            return 3;
        }

        return 2;
    }
}
