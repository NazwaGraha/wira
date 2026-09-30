<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationMember extends Model
{
    protected $fillable = [
        'name',
        'position',
        'subtitle',
        'level',
        'order_position',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'order_position' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function getLevelLabel(int $level): string
    {
        return match ($level) {
            1 => 'Tingkat 1: Pembina PMR',
            2 => 'Tingkat 2: Ketua Umum / Pimpinan',
            3 => 'Tingkat 3: Pengurus Harian (BPH)',
            4 => 'Tingkat 4: Koordinator Seksi / Divisi',
            default => 'Tingkat 5: Anggota / Divisi Lainnya',
        };
    }
}
