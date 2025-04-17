<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Threls\ThrelsTicketingModule\Enums\TicketRestrictionEnum;

class CustomRestrictionItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'key' => TicketRestrictionEnum::class,
    ];

    public function customRestriction(): BelongsTo
    {
        return $this->belongsTo(CustomRestriction::class);
    }

    public function ticketRestriction(): BelongsTo
    {
        return $this->belongsTo(TIcketRestriction::class);
    }

}
