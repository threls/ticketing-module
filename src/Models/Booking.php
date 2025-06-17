<?php

namespace Threls\ThrelsTicketingModule\Models;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Threls\ThrelsTicketingModule\Casts\MoneyCast;
use Threls\ThrelsTicketingModule\Enums\BookingStatusEnum;


/**
 * @property Money|null $amount
 * @property Money|null $vat_amount
 * @property Money|null $original_amount
 * @property Money|null $discount_amount
 */
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
            'original_amount' => MoneyCast::class,
            'discount_amount' => MoneyCast::class,
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

    public function bookingDiscounts(): HasMany
    {
        return $this->hasMany(BookingDiscount::class);
    }
}
