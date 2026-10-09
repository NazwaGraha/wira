<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'theme',
        'start_date',
        'end_date',
        'location',
        'description',
        'banner_image',
        'handbook_file',
        'registration_fee',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'is_active',
        'is_registration_open',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'is_registration_open' => 'boolean',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(CompetitionCategory::class)->orderBy('order_position');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function infoMenus(): HasMany
    {
        return $this->hasMany(CompetitionInfoMenu::class)->orderBy('order_position');
    }
}
