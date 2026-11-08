<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_event_id',
        'registration_code',
        'school_name',
        'level',
        'advisor_name',
        'advisor_phone',
        'advisor_email',
        'school_address',
        'payment_proof',
        'total_payment',
        'registration_wave',
        'status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'is_checked_in',
        'checked_in_at',
        'checked_in_by',
        'checkin_notes',
        'parking_quota_mode',
        'custom_quota_r4',
        'custom_quota_r2',
        'parking_notes',
        'parking_priority_score',
        'parking_priority_rank',
        'parking_priority_criteria',
    ];

    protected $casts = [
        'total_payment' => 'decimal:2',
        'registration_wave' => 'integer',
        'verified_at' => 'datetime',
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
        'custom_quota_r4' => 'integer',
        'custom_quota_r2' => 'integer',
        'parking_priority_score' => 'decimal:2',
        'parking_priority_rank' => 'integer',
        'parking_priority_criteria' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompetitionEvent::class, 'competition_event_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(CompetitionParticipantTeam::class);
    }

    public function parkingTickets(): HasMany
    {
        return $this->hasMany(ParkingTicket::class, 'competition_registration_id');
    }

    /**
     * Alias accessor untuk total biaya pendaftaran lomba yang dibayarkan ke panitia.
     */
    public function getTotalFeeAttribute(): float
    {
        return (float) ($this->total_payment ?? 0);
    }

    public function getTotalTeamsCountAttribute(): int
    {
        return $this->teams()->where(function ($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->count();
    }

    public function getAllocatedQuotaR4Attribute(): int
    {
        if ($this->parking_quota_mode === 'custom' && ! is_null($this->custom_quota_r4)) {
            return (int) $this->custom_quota_r4;
        }

        return ParkingQuotaSetting::current()->calculateQuota($this->total_teams_count)['quota_r4'];
    }

    public function getAllocatedQuotaR2Attribute(): int
    {
        if ($this->parking_quota_mode === 'custom' && ! is_null($this->custom_quota_r2)) {
            return (int) $this->custom_quota_r2;
        }

        return ParkingQuotaSetting::current()->calculateQuota($this->total_teams_count)['quota_r2'];
    }

    public function getIssuedTicketsR4CountAttribute(): int
    {
        return $this->parkingTickets()
            ->where('is_public', false)
            ->whereIn('vehicle_type', ['r4_car', 'r4_elf', 'r4_bus'])
            ->whereIn('status', ['booked', 'parked'])
            ->count();
    }

    public function getIssuedTicketsR2CountAttribute(): int
    {
        return $this->parkingTickets()
            ->where('is_public', false)
            ->where('vehicle_type', 'r2_motor')
            ->whereIn('status', ['booked', 'parked'])
            ->count();
    }

    /**
     * Batas akhir klaim kuota parkir resmi gratis kontingen (H+1 penutupan pendaftaran gelombang 2).
     */
    public function getParkingRegistrationDeadlineAttribute(): ?Carbon
    {
        $event = $this->event ?: CompetitionEvent::where('is_active', true)->first();
        if (! $event) {
            return null;
        }

        if ($event->is_waves_enabled && $event->wave2_end_date) {
            return $event->wave2_end_date->copy()->addDay()->endOfDay();
        }

        if ($event->wave2_end_date) {
            return $event->wave2_end_date->copy()->addDay()->endOfDay();
        }

        if ($event->wave1_end_date) {
            return $event->wave1_end_date->copy()->addDay()->endOfDay();
        }

        if ($event->end_date) {
            return $event->end_date->copy()->addDay()->endOfDay();
        }

        return null;
    }

    /**
     * Cek apakah batas waktu pendaftaran parkir gratis kontingen telah terlewati.
     */
    public function getIsParkingRegistrationExpiredAttribute(): bool
    {
        $deadline = $this->parking_registration_deadline;

        return $deadline ? now()->gt($deadline) : false;
    }

    public function getRemainingQuotaR4Attribute(): int
    {
        if ($this->is_parking_registration_expired) {
            return 0;
        }

        return max(0, $this->allocated_quota_r4 - $this->issued_tickets_r4_count);
    }

    public function getRemainingQuotaR2Attribute(): int
    {
        if ($this->is_parking_registration_expired) {
            return 0;
        }

        return max(0, $this->allocated_quota_r2 - $this->issued_tickets_r2_count);
    }

    public function getQuotaSourceLabelAttribute(): string
    {
        if ($this->parking_quota_mode === 'custom' && ! is_null($this->custom_quota_r4)) {
            return 'Kustom Admin (Dispensasi)';
        }

        return ParkingQuotaSetting::current()->calculateQuota($this->total_teams_count)['tier_label'];
    }

    /**
     * URL Resolusi Bukti Pembayaran yang aman (menggunakan named route backoffice atau asset lokal).
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        if (empty($this->payment_proof)) {
            return null;
        }

        if (str_starts_with($this->payment_proof, 'http://') || str_starts_with($this->payment_proof, 'https://')) {
            return $this->payment_proof;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $this->payment_proof), '/');

        // Jika rute admin tersedia dan ID ada, utamakan rute aman controller
        if ($this->id) {
            return route('admin.competition-registrations.proof', $this->id);
        }

        return asset('storage/'.$cleanPath);
    }

    /**
     * Generate format nomor registrasi unik resmi lomba (Contoh: SBB-W54342H)
     */
    public static function generateRegistrationCode(?string $level = null): string
    {
        $prefixChar = 'R';
        if ($level) {
            $lvl = strtolower(trim($level));
            if ($lvl === 'wira') {
                $prefixChar = 'W';
            } elseif ($lvl === 'madya') {
                $prefixChar = 'M';
            } elseif ($lvl === 'mula') {
                $prefixChar = 'U';
            } else {
                $prefixChar = strtoupper(substr($lvl, 0, 1));
            }
        } else {
            $prefixChar = chr(mt_rand(65, 90));
        }

        do {
            $randomDigits = str_pad((string) mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            $suffixChar = chr(mt_rand(65, 90));
            $code = "SBB-{$prefixChar}{$randomDigits}{$suffixChar}";
        } while (static::where('registration_code', $code)->exists());

        return $code;
    }

    /**
     * Dapatkan tautan link grup WhatsApp peserta resmi sesuai jenjang tingkatan:
     * - Tingkat Mula: https://chat.whatsapp.com/BUenufvlBK0EHOfpsKpoLi
     * - Tingkat Madya: https://chat.whatsapp.com/KrkdA8yYjraDxrD0TyeJYj
     * - Tingkat Wira: https://chat.whatsapp.com/BnsSIbpvpRxKsgUlp6YSYT
     */
    public function getWhatsappGroupUrlAttribute(): string
    {
        $lvl = strtolower(trim($this->level ?? ''));

        if ($lvl === 'mula') {
            return 'https://chat.whatsapp.com/BUenufvlBK0EHOfpsKpoLi';
        }

        if ($lvl === 'madya') {
            return 'https://chat.whatsapp.com/KrkdA8yYjraDxrD0TyeJYj';
        }

        // Default Wira
        return 'https://chat.whatsapp.com/BnsSIbpvpRxKsgUlp6YSYT';
    }

    public function getWhatsappGroupUrl(): string
    {
        return $this->getWhatsappGroupUrlAttribute();
    }
}
