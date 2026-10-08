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
