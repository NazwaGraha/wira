<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationMember extends Model
{
    protected $fillable = [
        'member_id',
        'name',
        'position',
        'subtitle',
        'level',
        'order_position',
        'icon',
        'photo',
        'staff_members',
        'work_program',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'order_position' => 'integer',
        'is_active' => 'boolean',
        'staff_members' => 'array',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!empty($this->photo)) {
            return str_starts_with($this->photo, 'http') || str_starts_with($this->photo, '/')
                ? $this->photo
                : asset('storage/' . $this->photo);
        }

        if ($this->member && !empty($this->member->photo)) {
            return str_starts_with($this->member->photo, 'http') || str_starts_with($this->member->photo, '/')
                ? $this->member->photo
                : asset('storage/' . $this->member->photo);
        }

        // Automatic lookup in Member by name if member_id is not explicitly set
        if (!empty($this->name)) {
            $matchedMember = Member::where('name', $this->name)->first();
            if ($matchedMember && !empty($matchedMember->photo)) {
                return str_starts_with($matchedMember->photo, 'http') || str_starts_with($matchedMember->photo, '/')
                    ? $matchedMember->photo
                    : asset('storage/' . $matchedMember->photo);
            }
        }

        if (!empty($this->name)) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc2626&color=ffffff&bold=true&size=256';
        }

        return null;
    }

    public static function getLevelLabel(int $level): string
    {
        return match ($level) {
            1 => 'Tingkat 1: Pembina PMR',
            2 => 'Tingkat 2: Ketua Umum / Pimpinan',
            3 => 'Tingkat 3: Pengurus Harian (BPH)',
            4 => 'Tingkat 4: 5 Bidang Utama',
            default => 'Tingkat 5: Anggota / Divisi Lainnya',
        };
    }
}
