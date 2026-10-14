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
}
