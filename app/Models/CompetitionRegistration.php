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
    ];

    protected $casts = [
        'total_payment' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompetitionEvent::class, 'competition_event_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(CompetitionParticipantTeam::class);
    }
}
