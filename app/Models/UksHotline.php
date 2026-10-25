<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class UksHotline extends Model
{
    use HasFactory;

    protected $table = 'uks_hotlines';

    protected $fillable = [
        'phone_number',
        'label',
        'service_name',
        'officer_name',
        'operational_hours',
        'location',
        'is_active',
        'status_text',
        'description',
        'whatsapp_message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Dapatkan data Hotline UKS aktif saat ini (dengan fallback aman jika tabel belum dimigrasi).
     */
    public static function current(): self
    {
        try {
            if (Schema::hasTable('uks_hotlines')) {
                $hotline = static::first();
                if ($hotline) {
                    if (str_contains((string) $hotline->phone_number, '857') || empty($hotline->phone_number)) {
                        $hotline->phone_number = '083872506817';
                        $hotline->save();
                    }

                    return $hotline;
                }
            }
        } catch (\Throwable $e) {
            // Abaikan error koneksi db saat fallback
        }

        // Default instance fallback
        return new static([
            'phone_number' => '083872506817',
            'label' => 'Hotline UKS',
            'service_name' => 'Piket Medis & P3K Sekolah',
            'officer_name' => 'Tim Relawan PMR Wira SMAN 1 Ciawi',
            'operational_hours' => 'Senin - Jumat (07.00 - 16.00 WIB)',
            'location' => 'Ruang UKS SMAN 1 Ciawi',
            'is_active' => true,
            'status_text' => 'Siaga di Jam Sekolah',
            'description' => 'Layanan cepat tanggap pertolongan pertama (P3K), penanganan siswa sakit/pingsan saat upacara/KBM, serta evakuasi medis darurat di lingkungan SMAN 1 Ciawi.',
            'whatsapp_message' => 'Halo Petugas Piket Medis PMR SMAN 1 Ciawi, saya membutuhkan bantuan medis/pertolongan pertama di lokasi: ',
        ]);
    }

    /**
     * Format nomor HP bersih (hanya angka dengan awalan 62).
     */
    public function getCleanPhoneAttribute(): string
    {
        $raw = preg_replace('/[^0-9]/', '', (string) $this->phone_number);
        if (str_starts_with($raw, '0')) {
            return '62'.substr($raw, 1);
        }
        if (str_starts_with($raw, '62')) {
            return $raw;
        }

        return $raw ? '62'.$raw : '6283872506817';
    }

    /**
     * Format tampilan nomor HP yang nyaman dibaca (contoh: 0838-7250-6817).
     */
    public function getFormattedPhoneAttribute(): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $this->phone_number);
        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        if (strlen($digits) >= 10 && strlen($digits) <= 13) {
            return substr($digits, 0, 4).'-'.substr($digits, 4, 4).'-'.substr($digits, 8);
        }

        return (string) $this->phone_number;
    }

    /**
     * Tautan WhatsApp langsung dengan prefilled message.
     */
    public function getWhatsappUrlAttribute(): string
    {
        $phone = $this->clean_phone;
        $msg = rawurlencode($this->whatsapp_message ?: 'Halo Petugas Piket Medis PMR SMAN 1 Ciawi, saya membutuhkan bantuan pertolongan pertama di lokasi: ');

        return "https://wa.me/{$phone}?text={$msg}";
    }

    /**
     * Tautan panggilan telepon langsung (tel:).
     */
    public function getTelUrlAttribute(): string
    {
        return 'tel:+'.$this->clean_phone;
    }
}
