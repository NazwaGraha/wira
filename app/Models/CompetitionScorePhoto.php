<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionScorePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_score_id',
        'competition_participant_team_id',
        'photo_path',
        'caption',
        'file_size',
    ];

    public function score(): BelongsTo
    {
        return $this->belongsTo(CompetitionScore::class, 'competition_score_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(CompetitionParticipantTeam::class, 'competition_participant_team_id');
    }

    public function getPhotoUrlAttribute(): string
    {
        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        $clean = ltrim(str_replace('storage/', '', $this->photo_path), '/');

        return asset('storage/'.$clean);
    }
}
