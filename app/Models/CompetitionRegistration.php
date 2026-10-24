<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'total_payment' => 'decimal:2',
        'registration_wave' => 'integer',
        'verified_at' => 'datetime',
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompetitionEvent::class, 'competition_event_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(CompetitionParticipantTeam::class);
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
}
