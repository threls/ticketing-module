<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

class BookingClientDetail extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('booking'));
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['first_name'].' '.$attributes['last_name']
        );
    }
}
