<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_category_id',
        'competition_participant_team_id',
        'round_name',
        'score_details',
        'final_score',
        'time_recorded',
        'rank',
        'is_disqualified',
        'notes',
    ];

    protected $casts = [
        'score_details' => 'array',
        'final_score' => 'decimal:2',
        'rank' => 'integer',
        'is_disqualified' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CompetitionCategory::class, 'competition_category_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(CompetitionParticipantTeam::class, 'competition_participant_team_id');
    }
}
