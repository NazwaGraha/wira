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

#[Fillable(['name', 'username', 'email', 'password', 'role', 'phone'])]
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
}
