<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingFareSetting extends Model
{
    use HasFactory;

    protected $table = 'parking_fare_settings';

    protected $fillable = [
        'is_public_parking_active',
        'fare_r2_motor',
        'fare_r4_car',
        'fare_r4_minibus',
        'fare_r6_bus',
        'allow_cash',
        'allow_qris',
        'lost_ticket_penalty',
        'ticket_receipt_note',
    ];

    protected $casts = [
        'is_public_parking_active' => 'boolean',
        'fare_r2_motor' => 'integer',
        'fare_r4_car' => 'integer',
        'fare_r4_minibus' => 'integer',
        'fare_r6_bus' => 'integer',
        'allow_cash' => 'boolean',
        'allow_qris' => 'boolean',
        'lost_ticket_penalty' => 'integer',
    ];

    /**
     * Singleton instance helper
     */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'is_public_parking_active' => true,
                'fare_r2_motor' => 5000,
                'fare_r4_car' => 10000,
                'fare_r4_minibus' => 25000,
                'fare_r6_bus' => 50000,
                'allow_cash' => true,
                'allow_qris' => true,
                'lost_ticket_penalty' => 20000,
                'ticket_receipt_note' => 'Karcis parkir wajib disimpan sebagai bukti keluar. Kunci ganda kendaraan Anda.',
            ]
        );
    }

    /**
     * Dapatkan tarif nominal berdasarkan jenis kendaraan
     */
    public function getFareForVehicleType(string $type): int
    {
        return match ($type) {
            'r2_motor' => $this->fare_r2_motor,
            'r4_car' => $this->fare_r4_car,
            'r4_elf' => $this->fare_r4_minibus,
            'r4_bus' => $this->fare_r6_bus,
            default => $this->fare_r4_car,
        };
    }
}
