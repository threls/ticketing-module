<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Threls\ThrelsTicketingModule\Enums\TicketRestrictionEnum;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

class CustomRestrictionItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'key' => TicketRestrictionEnum::class,
    ];

    public function customRestriction(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('customRestriction'));
    }

    public function ticketRestriction(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('ticketRestriction'));
    }
}
