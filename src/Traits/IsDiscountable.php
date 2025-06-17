<?php

namespace Threls\ThrelsTicketingModule\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Threls\ThrelsTicketingModule\Models\BookingDiscount;

trait IsDiscountable
{
    public function bookingDiscounts(): MorphMany
    {
        return $this->morphMany(BookingDiscount::class, 'discountable');
    }
}
