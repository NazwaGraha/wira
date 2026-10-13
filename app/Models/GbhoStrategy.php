<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GbhoStrategy extends Model
{
    protected $fillable = [
        'organization_guideline_id',
        'order_number',
        'title',
        'description',
        'icon',
    ];

    public function guideline(): BelongsTo
    {
        return $this->belongsTo(OrganizationGuideline::class, 'organization_guideline_id');
    }
}
