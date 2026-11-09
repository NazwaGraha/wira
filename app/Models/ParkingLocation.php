<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingLocation extends Model
{
    use HasFactory;

    protected $table = 'parking_locations';

    protected $fillable = [
        'name',
        'code',
        'address',
        'gmaps_url',
        'capacity_r4',
        'capacity_r2',
        'is_priority',
        'priority_order',
        'officer_name',
        'officer_phone',
        'status',
        'notes',
    ];

    protected $casts = [
        'capacity_r4' => 'integer',
        'capacity_r2' => 'integer',
        'is_priority' => 'boolean',
        'priority_order' => 'integer',
    ];

    public function officers(): BelongsToMany
    {
        return $this->belongsToMany(
            ParkingOfficer::class,
            'parking_officer_locations',
            'parking_location_id',
            'parking_officer_id'
        )->withTimestamps();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(ParkingTicket::class, 'parking_location_id');
    }

    public function activeTickets(): HasMany
    {
        return $this->tickets()->whereIn('status', ['booked', 'parked']);
    }

    public function getBookedR4CountAttribute(): int
    {
        return $this->tickets()
            ->whereIn('vehicle_type', ['r4_car', 'r4_elf', 'r4_bus'])
            ->whereIn('status', ['booked', 'parked'])
            ->count();
    }

    public function getBookedR2CountAttribute(): int
    {
        return $this->tickets()
            ->where('vehicle_type', 'r2_motor')
            ->whereIn('status', ['booked', 'parked'])
            ->count();
    }

    public function getParkedR4CountAttribute(): int
    {
        return $this->tickets()
            ->whereIn('vehicle_type', ['r4_car', 'r4_elf', 'r4_bus'])
            ->where('status', 'parked')
            ->count();
    }

    public function getParkedR2CountAttribute(): int
    {
        return $this->tickets()
            ->where('vehicle_type', 'r2_motor')
            ->where('status', 'parked')
            ->count();
    }

    public function getRemainingR4Attribute(): int
    {
        return max(0, $this->capacity_r4 - $this->booked_r4_count);
    }

    public function getRemainingR2Attribute(): int
    {
        return max(0, $this->capacity_r2 - $this->booked_r2_count);
    }

    public function getPercentR4Attribute(): int
    {
        if ($this->capacity_r4 <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->booked_r4_count / $this->capacity_r4) * 100));
    }

    public function getPercentR2Attribute(): int
    {
        if ($this->capacity_r2 <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->booked_r2_count / $this->capacity_r2) * 100));
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'full' => 'Penuh',
            'closed' => 'Ditutup',
            default => 'Tersedia',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'full' => 'bg-rose-50 text-rose-700 border-rose-200',
            'closed' => 'bg-slate-100 text-slate-700 border-slate-300',
            default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }
}
