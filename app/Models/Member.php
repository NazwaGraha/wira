<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'position',
        'address',
        'birth_place',
        'birth_date',
        'gender',
        'class_grade',
        'entry_year',
        'member_status',
        'email',
        'phone',
        'motto',
        'vision',
        'mission',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'entry_year' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Otomatis tentukan status: jika tahun masuk + 3 <= tahun sekarang maka Purna
     */
    public function getEffectiveStatusAttribute(): string
    {
        if ($this->entry_year) {
            $currentYear = (int) date('Y');
            if ($currentYear >= ($this->entry_year + 3)) {
                return 'Purna';
            }
        }

        return $this->member_status ?: 'Aktif';
    }

    public function getPhotoUrlAttribute(): string
    {
        if (!empty($this->photo)) {
            return str_starts_with($this->photo, 'http') || str_starts_with($this->photo, '/')
                ? $this->photo
                : asset('storage/' . $this->photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc2626&color=ffffff&bold=true&size=256';
    }
}
