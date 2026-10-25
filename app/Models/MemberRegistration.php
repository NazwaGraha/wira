<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'class_grade',
        'nisn',
        'phone',
        'email',
        'interest_field',
        'motivation',
        'status',
    ];

    /**
     * URL WhatsApp langsung ke calon anggota dengan pesan salam resmi.
     */
    public function getWhatsappUrlAttribute(): string
    {
        $raw = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if (str_starts_with($raw, '0')) {
            $raw = '62'.substr($raw, 1);
        } elseif (! str_starts_with($raw, '62')) {
            $raw = '62'.$raw;
        }
        $msg = urlencode("Halo {$this->full_name}, kami dari Pengurus PMR Wira SMAN 1 Ciawi terkait formulir pendaftaran Calon Anggota Baru Anda...");

        return "https://wa.me/{$raw}?text={$msg}";
    }

    /**
     * Konfigurasi warna & label status badge.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'verified' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300', 'label' => 'Terverifikasi'],
            'accepted' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300', 'label' => 'Diterima'],
            'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'border' => 'border-rose-300', 'label' => 'Ditolak'],
            default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'border' => 'border-amber-300', 'label' => 'Menunggu'],
        };
    }
}
