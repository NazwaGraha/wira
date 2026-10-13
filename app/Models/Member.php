<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nri',
        'nip',
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
        'password',
        'phone',
        'motto',
        'vision',
        'mission',
        'photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'entry_year' => 'integer',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Accessor untuk NRI (Nomor Registrasi Induk)
     * Mengembalikan kolom 'nri' atau fallback ke 'nis'
     */
    public function getNriAttribute(): ?string
    {
        return $this->attributes['nri'] ?? $this->attributes['nis'] ?? null;
    }

    /**
     * Backward-compatibility accessor untuk NIS
     */
    public function getNisAttribute(): ?string
    {
        return $this->attributes['nri'] ?? $this->attributes['nis'] ?? null;
    }

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
        if (! empty($this->photo)) {
            return str_starts_with($this->photo, 'http') || str_starts_with($this->photo, '/')
                ? $this->photo
                : asset('storage/'.$this->photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=dc2626&color=ffffff&bold=true&size=256';
    }

    /**
     * Pengecekan apakah user adalah Super User
     */
    public function isSuperUser(): bool
    {
        return ($this->email === 'nazwagraha@gmail.com') || ($this->id === 'superuser') || ! empty($this->is_super_user);
    }

    /**
     * Dapatkan kode role anggota
     */
    public function getRoleAttribute(): string
    {
        if ($this->isSuperUser()) {
            return 'superuser';
        }

        $pos = strtolower(trim($this->position ?? ''));

        if (str_contains($pos, 'pembina')) {
            return 'pembina';
        }
        if (str_contains($pos, 'ketua umum') || str_contains($pos, 'wakil ketua')) {
            return 'ketua_umum';
        }
        if (str_contains($pos, 'sekretaris') || str_contains($pos, 'bendahara')) {
            return 'bph';
        }
        if (str_contains($pos, 'ketua bidang') || str_contains($pos, 'koordinator')) {
            return 'ketua_bidang';
        }
        if (str_contains($pos, 'staf') || str_contains($pos, 'staff')) {
            return 'staff_bidang';
        }

        return 'anggota';
    }

    /**
     * Label representasi role
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'superuser' => 'Super Administrator',
            'pembina' => 'Pembina PMR',
            'ketua_umum' => 'Ketua Umum / Pimpinan',
            'bph' => 'Pengurus Harian (BPH)',
            'ketua_bidang' => 'Ketua Bidang',
            'staff_bidang' => 'Staff Bidang',
            default => 'Anggota PMR Wira',
        };
    }

    /**
     * Hak Akses Hapus (Delete)
     * Hanya Super User, Pembina, Ketum, BPH (Sekretaris & Bendahara) yang boleh DELETE
     */
    public function canDelete(): bool
    {
        return in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph'], true);
    }

    /**
     * Apakah akses dibatasi hanya untuk Modul Artikel
     * Staff 5 Bidang hanya memiliki wewenang CRU Artikel
     */
    public function canOnlyManageArticles(): bool
    {
        return $this->role === 'staff_bidang';
    }

    /**
     * Apakah berhak mengakses Backoffice
     */
    public function canAccessBackoffice(): bool
    {
        return in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph', 'ketua_bidang', 'staff_bidang'], true);
    }
}
