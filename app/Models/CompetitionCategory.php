<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'is_photo_required',
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
        'is_photo_required' => 'boolean',
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

    public function assignedJuries(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'jury_category_assignments',
            'competition_category_id',
            'user_id'
        )->withTimestamps();
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

    /**
     * Hitung perolehan poin juara umum berdasarkan peringkat (rank):
     * - Tier 1 (LPP, LCT, Pendidikan Remaja Sebaya, Olimpiade Kepalangmerahan, Mading):
     *   Juara 1: 12 poin, Juara 2: 10 poin, Juara 3: 8 poin, Juara Harapan 1: 6 poin, Juara Harapan 2: 4 poin
     * - Tier 2 (LKTR, LKCT, Lomba Mewarnai):
     *   Juara 1: 10 poin, Juara 2: 8 poin, Juara 3: 6 poin, Juara Harapan 1: 4 poin, Juara Harapan 2: 2 poin
     * - Tier 3 (PMR Favorite):
     *   Juara 1: 3 poin, Juara 2: 2 poin, Juara 3: 1 poin
     */
    public function getPointsForRank(?int $rank): int
    {
        if (! $rank || $rank < 1 || $rank > 5) {
            return 0;
        }

        $tier = $this->point_tier;
        $name = strtolower($this->name ?? '');
        $code = strtoupper($this->code ?? '');

        if (! $tier) {
            if (str_contains($name, 'tandu') || str_contains($name, 'cuci tangan') || str_contains($name, 'mewarnai') || str_starts_with($code, 'LKTR') || str_starts_with($code, 'LKCT') || str_starts_with($code, 'MEWARNAI')) {
                $tier = 'tier_2';
            } elseif (str_contains($name, 'favorite') || str_contains($name, 'favorit') || str_starts_with($code, 'FAV')) {
                $tier = 'tier_3';
            } else {
                $tier = 'tier_1';
            }
        }

        if ($tier === 'tier_1') {
            return match ($rank) {
                1 => 12,
                2 => 10,
                3 => 8,
                4 => 6,
                5 => 4,
                default => 0,
            };
        }

        if ($tier === 'tier_2') {
            return match ($rank) {
                1 => 10,
                2 => 8,
                3 => 6,
                4 => 4,
                5 => 2,
                default => 0,
            };
        }

        if ($tier === 'tier_3') {
            return match ($rank) {
                1 => 3,
                2 => 2,
                3 => 1,
                default => 0,
            };
        }

        if ($tier === 'tier_4') {
            return 0;
        }

        return 0;
    }

    /**
     * Dapatkan label representasi aturan poin juara umum.
     */
    public function getPointTierLabel(): string
    {
        return match ($this->point_tier) {
            'tier_1' => '12 / 10 / 8 / 6 / 4 Poin',
            'tier_2' => '10 / 8 / 6 / 4 / 2 Poin',
            'tier_3' => '3 / 2 / 1 Poin',
            'tier_4' => '0 Poin',
            default => '12 / 10 / 8 / 6 / 4 Poin',
        };
    }

    /**
     * Generate nomor urut tampil berikutnya berdasarkan Skema 1 (Pemisahan Sesi Regu A, Regu B, Regu C):
     * - Regu A (slot 0) -> A-01, A-02, A-03, dst.
     * - Regu B (slot 1) -> B-01, B-02, B-03, dst.
     * - Regu C (slot 2) -> C-01, C-02, C-03, dst.
     *
     * @param  int  $slotIndex  Indeks regu sekolah untuk kategori ini (0 untuk Regu A, 1 untuk Regu B, dst)
     * @param  string|null  $label  Label regu (misal: "Regu A", "Regu B", "Regu Putra")
     */
    public function generateNextOrderNumber(int $slotIndex = 0, ?string $label = null): string
    {
        $prefix = 'A';
        if ($slotIndex > 0) {
            $prefix = chr(65 + $slotIndex);
        } elseif ($label) {
            if (preg_match('/\b(?:regu\s+)?([B-Z])\b/i', $label, $matches)) {
                $prefix = strtoupper($matches[1]);
            }
        }

        $existingOrders = CompetitionParticipantTeam::where('competition_category_id', $this->id)
            ->where(function ($q) use ($prefix) {
                $q->where('order_number', 'LIKE', "{$prefix}-%")
                    ->orWhere('order_number', 'LIKE', "%-{$prefix}%");
            })
            ->pluck('order_number');

        $maxNum = 0;
        foreach ($existingOrders as $ord) {
            if (preg_match('/'.preg_quote($prefix, '/').'[-]?(\d+)/i', (string) $ord, $matches)) {
                $val = intval($matches[1]);
                if ($val > $maxNum) {
                    $maxNum = $val;
                }
            }
        }

        $nextNum = $maxNum + 1;

        return sprintf('%s-%02d', $prefix, $nextNum);
    }
}
