<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'phone', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function assignedCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            CompetitionCategory::class,
            'jury_category_assignments',
            'user_id',
            'competition_category_id'
        )->withTimestamps();
    }

    public function isJury(): bool
    {
        return $this->role === 'jury';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superuser', 'admin']);
    }

    public function canAccessBackoffice(): bool
    {
        return $this->isAdmin();
    }

    public function canDelete(): bool
    {
        return $this->isAdmin();
    }

    public function canOnlyManageArticles(): bool
    {
        return false;
    }

    public function canManageMembers(): bool
    {
        return $this->isAdmin();
    }

    public function isPurna(): bool
    {
        return false;
    }

    public function isAktif(): bool
    {
        return true;
    }

    public function isSuperUser(): bool
    {
        return $this->role === 'superuser';
    }

    public function hasCustomPermissions(): bool
    {
        return false;
    }

    public function canManagePermissions(): bool
    {
        return $this->isAdmin() || $this->isSuperUser();
    }

    public function hasPermission(string $permissionCode): bool
    {
        if ($this->isAdmin() || $this->isSuperUser()) {
            return true;
        }

        return false;
    }

    public function canDeleteData(): bool
    {
        return $this->canDelete();
    }

    public function canExportData(): bool
    {
        return true;
    }

    public function getRoleAttribute(): string
    {
        return $this->attributes['role'] ?? 'admin';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'jury' => 'Juri Lapangan',
            'superuser' => 'Super Administrator',
            default => 'Administrator',
        };
    }

    public function getPhotoUrlAttribute(): string
    {
        if (! empty($this->photo)) {
            return str_starts_with($this->photo, 'http') || str_starts_with($this->photo, '/')
                ? $this->photo
                : asset('storage/'.$this->photo);
        }

        if ($this->role === 'superuser' || $this->name === 'Super Administrator') {
            return asset('images/logo-pmr.png');
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=dc2626&color=ffffff&bold=true&size=256';
    }
}
