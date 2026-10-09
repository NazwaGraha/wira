<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_event_id',
        'code',
        'name',
        'level',
        'gender_category',
        'scoring_type',
        'point_tier',
        'criteria_schema',
        'registration_fee',
        'max_team_members',
        'has_rounds',
        'order_position',
        'is_active',
    ];

    protected $casts = [
        'criteria_schema' => 'array',
        'registration_fee' => 'decimal:2',
        'max_team_members' => 'integer',
        'has_rounds' => 'boolean',
        'order_position' => 'integer',
        'is_active' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompetitionEvent::class, 'competition_event_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(CompetitionParticipantTeam::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionScore::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $gender = ($this->gender_category && $this->gender_category !== 'Umum') ? " {$this->gender_category}" : '';
        return "{$this->name} - {$this->level}{$gender}";
    }

    public function getRegistrationFeeAttribute($value)
    {
        return $value !== null ? $value : 150000;
    }

    /**
     * Check if a school is allowed to register multiple teams for this category.
     * Restriction rule: maximum 1 team Putra and 1 team Putri per school,
     * except for Cuci Tangan and Olimpiade (maximum 3 teams).
     */
    public function isMultiTeamAllowed(): bool
    {
        $name = strtolower($this->name ?? '');
        return str_contains($name, 'cuci tangan') || str_contains($name, 'olimpiade') || str_contains($name, 'olympiade');
    }

    /**
     * Get maximum teams allowed per school for this category.
     * Cuci Tangan and Olimpiade: max 3 teams.
     * Other categories: max 1 team per gender/school.
     */
    public function maxTeamsPerSchool(): int
    {
        return $this->isMultiTeamAllowed() ? 3 : 1;
    }
}
