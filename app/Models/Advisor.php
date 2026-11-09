<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Advisor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nip',
        'name',
        'position',
        'title_degree',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'email',
        'password',
        'motto',
        'vision',
        'mission',
        'photo',
        'order_position',
        'is_active',
        'custom_permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'order_position' => 'integer',
        'is_active' => 'boolean',
        'password' => 'hashed',
        'custom_permissions' => 'array',
    ];

    public function getPhotoUrlAttribute(): string
    {
        if (! empty($this->photo)) {
            return str_starts_with($this->photo, 'http') || str_starts_with($this->photo, '/')
                ? $this->photo
                : asset('storage/'.$this->photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=991b1b&color=ffffff&bold=true&size=256';
    }

    public function getRoleAttribute(): string
    {
        return 'pembina';
    }

    public function getRoleLabelAttribute(): string
    {
        return 'Pembina PMR';
    }

    public function canDelete(): bool
    {
        return true;
    }

    public function canOnlyManageArticles(): bool
    {
        return false;
    }

    public function canManageMembers(): bool
    {
        return true;
    }

    public function isPurna(): bool
    {
        return false;
    }

    public function isAktif(): bool
    {
        return (bool) $this->is_active;
    }

    public function canAccessBackoffice(): bool
    {
        return (bool) $this->is_active;
    }

    public function isSuperUser(): bool
    {
        return false;
    }

    public function hasCustomPermissions(): bool
    {
        return ! empty($this->custom_permissions)
            && is_array($this->custom_permissions)
            && ($this->custom_permissions['mode'] ?? '') === 'custom';
    }

    public function canManagePermissions(): bool
    {
        return (bool) $this->is_active;
    }

    public function hasPermission(string $permissionCode): bool
    {
        if ($this->hasCustomPermissions()) {
            $allowed = $this->custom_permissions['allowed_menus'] ?? [];

            return in_array($permissionCode, $allowed, true);
        }

        return true;
    }

    public function canDeleteData(): bool
    {
        if ($this->hasCustomPermissions()) {
            return ! empty($this->custom_permissions['can_delete']);
        }

        return true;
    }

    public function canExportData(): bool
    {
        if ($this->hasCustomPermissions()) {
            return ! empty($this->custom_permissions['can_export']);
        }

        return true;
    }
}
