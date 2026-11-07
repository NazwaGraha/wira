<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingQuotaSetting extends Model
{
    use HasFactory;

    protected $table = 'parking_quota_settings';

    protected $fillable = [
        'mode',
        'flat_default_r4',
        'flat_default_r2',
        'matrix_rules',
    ];

    protected $casts = [
        'flat_default_r4' => 'integer',
        'flat_default_r2' => 'integer',
        'matrix_rules' => 'array',
    ];

    public static function defaultMatrixRules(): array
    {
        return [
            [
                'tier' => 1,
                'label' => 'Tier 1 (1 - 2 Regu)',
                'min_teams' => 1,
                'max_teams' => 2,
                'quota_r4' => 1,
                'quota_r2' => 1,
                'desc' => 'Kontingen Minimal (1 Mobil & 1 Motor)',
            ],
            [
                'tier' => 2,
                'label' => 'Tier 2 (3 - 5 Regu)',
                'min_teams' => 3,
                'max_teams' => 5,
                'quota_r4' => 2,
                'quota_r2' => 2,
                'desc' => 'Kontingen Sedang (2 Mobil & 2 Motor)',
            ],
            [
                'tier' => 3,
                'label' => 'Tier 3 (6 - 8 Regu)',
                'min_teams' => 6,
                'max_teams' => 8,
                'quota_r4' => 3,
                'quota_r2' => 3,
                'desc' => 'Kontingen Banyak (3 Mobil / 1 Elf & 3 Motor)',
            ],
            [
                'tier' => 4,
                'label' => 'Tier 4 (≥ 9 Regu)',
                'min_teams' => 9,
                'max_teams' => 999,
                'quota_r4' => 4,
                'quota_r2' => 5,
                'desc' => 'Kontingen Akbar (4 Mobil / 1 Bus & 5 Motor)',
            ],
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'mode' => 'proportional_teams',
                'flat_default_r4' => 1,
                'flat_default_r2' => 2,
                'matrix_rules' => static::defaultMatrixRules(),
            ]
        );
    }

    public function calculateQuota(int $teamCount): array
    {
        if ($this->mode === 'flat') {
            return [
                'quota_r4' => (int) $this->flat_default_r4,
                'quota_r2' => (int) $this->flat_default_r2,
                'tier_label' => 'Mode Flat Standar',
                'source' => 'flat',
            ];
        }

        $rules = is_array($this->matrix_rules) ? $this->matrix_rules : static::defaultMatrixRules();

        foreach ($rules as $rule) {
            $min = (int) ($rule['min_teams'] ?? 1);
            $max = (int) ($rule['max_teams'] ?? 999);

            if ($teamCount >= $min && $teamCount <= $max) {
                return [
                    'quota_r4' => (int) ($rule['quota_r4'] ?? 1),
                    'quota_r2' => (int) ($rule['quota_r2'] ?? 1),
                    'tier_label' => $rule['label'] ?? "Tier ({$min}-{$max} Regu)",
                    'source' => 'proportional',
                ];
            }
        }

        // Default fallback if teamCount is 0 or outside
        return [
            'quota_r4' => (int) ($rules[0]['quota_r4'] ?? 1),
            'quota_r2' => (int) ($rules[0]['quota_r2'] ?? 1),
            'tier_label' => $rules[0]['label'] ?? 'Tier 1 (1 - 2 Regu)',
            'source' => 'proportional',
        ];
    }
}
