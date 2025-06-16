<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\ApplyBookingDiscountDto;
use Threls\ThrelsTicketingModule\Models\BookingDiscount;

class ApplyBookingDiscountAction
{
    public function execute(ApplyBookingDiscountDto $dto): BookingDiscount
    {
        $bookingDiscount = new BookingDiscount();
        $bookingDiscount->booking_id = $dto->booking->id;
        $bookingDiscount->discountable_id = $dto->discountableId;
        $bookingDiscount->discountable_type = $dto->discountableType;
        $bookingDiscount->amount = $dto->amount;
        $bookingDiscount->amount_currency = $dto->currency;
        $bookingDiscount->save();

        return $bookingDiscount;

    }

}
