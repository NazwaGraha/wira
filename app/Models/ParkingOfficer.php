<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ParkingOfficer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'parking_officers';

    protected $fillable = [
        'name',
        'username',
        'password',
        'phone',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Kantong parkir yang ditugaskan ke petugas ini
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(
            ParkingLocation::class,
            'parking_officer_locations',
            'parking_officer_id',
            'parking_location_id'
        )->withTimestamps();
    }

    /**
     * Cek apakah petugas ditugaskan ke lokasi tertentu
     */
    public function hasAssignedLocation(int $locationId): bool
    {
        return $this->locations->contains('id', $locationId);
    }

    /**
     * Daftar nama kantong parkir yang ditugaskan sebagai string ringkas
     */
    public function getAssignedLocationNamesAttribute(): string
    {
        if ($this->locations->isEmpty()) {
            return 'Belum Ditugaskan';
        }

        return $this->locations->pluck('name')->implode(', ');
    }
}
