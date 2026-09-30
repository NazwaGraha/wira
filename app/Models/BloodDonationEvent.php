<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodDonationEvent extends Model
{
    protected $fillable = [
        'title',
        'event_date',
        'time_start',
        'time_end',
        'location',
        'target_bags',
        'is_active',
        'description',
        'banner_image',
        'registration_link',
        'is_registration_link_active',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
        'is_registration_link_active' => 'boolean',
    ];
}
