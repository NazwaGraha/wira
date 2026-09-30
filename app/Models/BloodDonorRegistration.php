<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodDonorRegistration extends Model
{
    protected $fillable = [
        'blood_donation_event_id',
        'name',
        'email',
        'phone',
        'blood_type',
        'rhesus',
        'date_of_birth',
        'gender',
        'address',
        'last_donation_date',
        'status',
        'medical_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_donation_date' => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(BloodDonationEvent::class, 'blood_donation_event_id');
    }
}
