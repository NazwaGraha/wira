<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionParticipantTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_registration_id',
        'competition_category_id',
        'order_number',
        'team_name',
        'team_label',
        'members_list',
        'is_active',
    ];

    protected $casts = [
        'members_list' => 'array',
        'is_active' => 'boolean',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(CompetitionRegistration::class, 'competition_registration_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CompetitionCategory::class, 'competition_category_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionScore::class);
    }
}
