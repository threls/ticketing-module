<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Threls\ThrelsTicketingModule\Enums\TicketRestrictionEnum;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

class TicketRestriction extends Model
{
    use SoftDeletes;

    protected $casts = [
        'key' => TicketRestrictionEnum::class,
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('ticket'));
    }
}
