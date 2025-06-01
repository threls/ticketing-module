<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Threls\ThrelsTicketingModule\Casts\MoneyCast;
use Threls\ThrelsTicketingModule\Enums\BookingStatusEnum;

class Booking extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts()
    {
        return [
            'status' => BookingStatusEnum::class,
            'amount' => MoneyCast::class,
            'vat_amount' => MoneyCast::class,
        ];
    }

    public function bookingClient(): HasOne
    {
        return $this->hasOne(BookingClientDetail::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function bookingTickets(): HasMany
    {
        return $this->hasMany(BookingTicket::class);
    }
}
