<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\ApplyBookingDiscountDto;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingDiscount;

class ApplyBookingDiscountAction
{
    protected ApplyBookingDiscountDto $applyBookingDiscountDto;

    public function execute(ApplyBookingDiscountDto $dto): Booking
    {
        $this->applyBookingDiscountDto = $dto;

        $this->createBookingDiscount()
            ->updateBooking();

        return $dto->booking->fresh();

    }

    protected function createBookingDiscount(): static
    {
        $bookingDiscount = new BookingDiscount;
        $bookingDiscount->booking_id = $this->applyBookingDiscountDto->booking->id;
        $bookingDiscount->discountable_id = $this->applyBookingDiscountDto->discountableId;
        $bookingDiscount->discountable_type = $this->applyBookingDiscountDto->discountableType;
        $bookingDiscount->amount = $this->applyBookingDiscountDto->amount;
        $bookingDiscount->amount_currency = $this->applyBookingDiscountDto->currency;
        $bookingDiscount->email = $this->applyBookingDiscountDto->email;
        $bookingDiscount->save();

        return $this;
    }

    protected function updateBooking(): void
    {
        $this->applyBookingDiscountDto->booking->original_amount = $this->applyBookingDiscountDto->booking->amount;
        $this->applyBookingDiscountDto->booking->original_amount_currency = $this->applyBookingDiscountDto->currency;
        $this->applyBookingDiscountDto->booking->discount_amount = $this->applyBookingDiscountDto->amount;
        $this->applyBookingDiscountDto->booking->discount_amount_currency = $this->applyBookingDiscountDto->currency;
        $this->applyBookingDiscountDto->booking->amount = $this->applyBookingDiscountDto->booking->amount->minus($this->applyBookingDiscountDto->amount);
        $this->applyBookingDiscountDto->booking->save();

    }
}
