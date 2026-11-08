<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ParkingTicket extends Model
{
    use HasFactory;

    protected $table = 'parking_tickets';

    protected $fillable = [
        'ticket_code',
        'qr_code_token',
        'competition_registration_id',
        'parking_location_id',
        'vehicle_type',
        'license_plate',
        'vehicle_brand',
        'vehicle_color',
        'driver_name',
        'driver_phone',
        'status',
        'check_in_at',
        'check_out_at',
        'checked_by_user_id',
        'is_public',
        'fee_amount',
        'payment_method',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'is_public' => 'boolean',
        'fee_amount' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (empty($ticket->qr_code_token)) {
                $ticket->qr_code_token = Str::random(32);
            }
            if (empty($ticket->ticket_code)) {
                $prefix = str_starts_with($ticket->vehicle_type ?? '', 'r2') ? 'PKR2' : 'PKR4';
                $nextSeq = (static::max('id') ?? 0) + 1;
                $code = sprintf('%s-SBB4-%04d', $prefix, $nextSeq);
                while (static::where('ticket_code', $code)->exists()) {
                    $nextSeq++;
                    $code = sprintf('%s-SBB4-%04d', $prefix, $nextSeq);
                }
                $ticket->ticket_code = $code;
            }
        });
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(ParkingLocation::class, 'parking_location_id');
    }

    public function parkingLocation(): BelongsTo
    {
        return $this->belongsTo(ParkingLocation::class, 'parking_location_id');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(CompetitionRegistration::class, 'competition_registration_id');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }

    public function getVehicleTypeLabelAttribute(): string
    {
        return match ($this->vehicle_type) {
            'r2_motor' => 'Roda 2 Sepeda Motor',
            'r4_car' => 'Roda 4 Mobil Pribadi / MPV / SUV',
            'r4_elf' => 'Roda 4+ Minibus (Elf / HiAce)',
            'r4_bus' => 'Roda 6+ Bus Besar Pariwisata',
            default => 'Kendaraan',
        };
    }

    public function getVehicleTypeShortAttribute(): string
    {
        return str_starts_with($this->vehicle_type ?? '', 'r2') ? 'Roda 2' : 'Roda 4';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'parked' => 'Terparkir (Check-In)',
            'checked_out' => 'Selesai (Check-Out)',
            'cancelled' => 'Dibatalkan',
            default => 'Terjadwal / Belum Tiba',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'parked' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'checked_out' => 'bg-slate-100 text-slate-700 border-slate-300',
            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    public function getSchoolNameAttribute(): string
    {
        return $this->registration?->school_name ?? 'Umum / Penonton';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Tunai (Cash)',
            'qris' => 'QRIS Digital',
            default => 'Gratis Kontingen',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'unpaid' => 'Belum Lunas',
            default => 'Gratis',
        };
    }

    public function getFormattedFeeAttribute(): string
    {
        if ($this->fee_amount > 0) {
            return 'Rp '.number_format($this->fee_amount, 0, ',', '.');
        }

        return 'Gratis';
    }
}
