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
        'custom_permissions',
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
        'custom_permissions' => 'array',
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
        return (strtolower((string) $this->email) === 'nazwagraha@gmail.com')
            || (strtolower((string) $this->nri) === 'admin')
            || in_array((int) $this->id, [999999999, 888888888], true)
            || in_array((string) $this->id, ['999999999', '888888888', 'superuser', 'admin'], true)
            || ! empty($this->is_super_user);
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
     * Apakah anggota berstatus Purna
     */
    public function isPurna(): bool
    {
        if ($this->isSuperUser()) {
            return false;
        }

        $status = strtolower(trim((string) ($this->member_status ?? '')));
        if ($status === 'purna' || $status === 'alumni') {
            return true;
        }

        if ($this->entry_year) {
            $currentYear = (int) date('Y');
            if ($currentYear >= ($this->entry_year + 3)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apakah anggota berstatus Aktif
     */
    public function isAktif(): bool
    {
        return ! $this->isPurna();
    }

    /**
     * Hak Akses Hapus (Delete)
     * Hanya Super User, Pembina, Ketum, BPH (Sekretaris & Bendahara) yang boleh DELETE
     */
    public function canDelete(): bool
    {
        if ($this->isPurna()) {
            return false;
        }

        return in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph'], true);
    }

    /**
     * Apakah akses dibatasi hanya untuk Modul Artikel
     * Staff 5 Bidang memiliki wewenang Artikel & Data Anggota
     */
    public function canOnlyManageArticles(): bool
    {
        return $this->role === 'staff_bidang';
    }

    /**
     * Apakah berhak mengelola modul Data Anggota (Daftar & Input Anggota)
     * Semua anggota yang berstatus aktif bisa akses
     */
    public function canManageMembers(): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        return $this->isAktif();
    }

    /**
     * Apakah berhak mengakses Backoffice
     * Semua anggota berstatus aktif bisa akses backoffice (minimal untuk Input & Daftar Anggota).
     * Anggota berstatus Purna tidak diberikan akses ke backoffice.
     */
    public function canAccessBackoffice(): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        if ($this->isPurna()) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah user menggunakan kustomisasi hak akses spesifik (Granular)
     */
    public function hasCustomPermissions(): bool
    {
        return ! empty($this->custom_permissions)
            && is_array($this->custom_permissions)
            && ($this->custom_permissions['mode'] ?? '') === 'custom';
    }

    /**
     * Cek apakah user berhak mengelola modul Manajemen Hak Akses Pengguna
     * Hanya Super User, Pembina, dan Ketua Umum
     */
    public function canManagePermissions(): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        return in_array($this->role, ['superuser', 'pembina', 'ketua_umum'], true);
    }

    /**
     * Cek izin untuk permission code tertentu
     */
    public function hasPermission(string $permissionCode): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        if ($this->hasCustomPermissions()) {
            $allowed = $this->custom_permissions['allowed_menus'] ?? [];

            return in_array($permissionCode, $allowed, true);
        }

        // Bawaan jabatan (Default RBAC)
        return match ($permissionCode) {
            'dashboard.view' => true,
            'dashboard.clear_cache' => $this->isSuperUser() || in_array($this->role, ['pembina', 'ketua_umum'], true),
            'articles.view', 'articles.create', 'articles.edit' => in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph', 'ketua_bidang', 'staff_bidang'], true),
            'articles.delete' => $this->canDelete(),
            'categories.view', 'categories.manage' => in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph', 'ketua_bidang', 'staff_bidang'], true),
            'advisors.view', 'advisors.create', 'advisors.edit' => in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph'], true),
            'advisors.delete' => $this->canDelete(),
            'member_registrations.view', 'member_registrations.verify' => $this->canManageMembers(),
            'member_registrations.delete' => $this->canDelete(),
            'members.view', 'members.create', 'members.edit', 'members.print_kta', 'members.export' => $this->canManageMembers(),
            'members.delete' => $this->canDelete(),
            'content_setup.home', 'content_setup.about' => in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph', 'ketua_bidang'], true),
            'media_manager.view', 'media_manager.upload', 'popup_banner.manage', 'background_music.manage', 'uks_hotline.manage', 'activities.view', 'activities.manage', 'gallery.view', 'gallery.manage', 'organization.view', 'organization.manage', 'prestasi.view', 'prestasi.manage', 'organization_guidelines.view', 'organization_guidelines.manage' => in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph', 'ketua_bidang'], true),
            'media_manager.delete' => $this->canDelete(),
            'competition_event.view', 'competition_event.manage', 'competition_bank_accounts.manage', 'competition_fees.manage', 'competition_quotas.manage', 'competition_registrations.view', 'competition_registrations.verify', 'competition_registrations.export', 'competition_checkin.view', 'competition_checkin.scan', 'competition_checkin.cancel', 'competition_juries.view', 'competition_juries.manage', 'competition_scores.view', 'competition_scores.input', 'competition_scores.master_sheet', 'competition_scores.export', 'competition_mascots.manage', 'competition_leaderboard.view', 'competition_leaderboard.publish', 'competition_participants.view', 'competition_participants.manage', 'competition_broadcast.send', 'competition_info_menus.manage' => in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph', 'ketua_bidang'], true),
            'parking_locations.view', 'parking_locations.manage', 'parking_tickets.view', 'parking_tickets.assign', 'parking_tickets.scan', 'parking_tickets.print', 'parking_quotas.manage', 'parking.fares_manage', 'parking.export' => in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph', 'ketua_bidang'], true),
            'blood_stocks.view', 'blood_stocks.update', 'blood_donation_events.view', 'blood_donation_events.manage', 'blood_donor_registrations.view', 'blood_donor_registrations.verify', 'blood_donor_registrations.export' => in_array($this->role, ['superuser', 'pembina', 'ketua_umum', 'bph', 'ketua_bidang'], true),
            'user_permissions.view', 'user_permissions.manage' => $this->canManagePermissions(),
            default => false,
        };
    }

    /**
     * Cek apakah user boleh melakukan aksi Hapus Data (DELETE)
     */
    public function canDeleteData(): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        if ($this->hasCustomPermissions()) {
            return ! empty($this->custom_permissions['can_delete']);
        }

        return $this->canDelete();
    }

    /**
     * Cek apakah user boleh melakukan aksi Cetak / Export ke Excel
     */
    public function canExportData(): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        if ($this->hasCustomPermissions()) {
            return ! empty($this->custom_permissions['can_export']);
        }

        return true;
    }
}
