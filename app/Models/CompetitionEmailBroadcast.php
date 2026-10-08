<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionEmailBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_event_id',
        'subject',
        'headline',
        'content',
        'button_text',
        'button_url',
        'target_scope',
        'target_level',
        'target_status',
        'recipient_count',
        'recipients_data',
        'sent_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'recipients_data' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompetitionEvent::class, 'competition_event_id');
    }
}
