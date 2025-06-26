<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

class CustomRestriction extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('ticket'));
    }

    public function customRestrictionItems(): HasMany
    {
        return $this->hasMany(TicketingModelResolverManager::getModelClass('customRestrictionItem'));
    }
}
