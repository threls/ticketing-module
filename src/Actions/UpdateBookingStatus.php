<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Threls\ThrelsTicketingModule\Enums\BookingStatusEnum;
use Threls\ThrelsTicketingModule\Models\Booking;

class UpdateBookingStatus
{
    public function execute(Booking $booking, BookingStatusEnum $status): void
    {
        if (! $booking->status->canTransitionTo($status)) {
            throw new BadRequestHttpException('Booking status cannot be transitioned to '.$status->value);
        }

        $booking->update(['status' => $status->value]);

    }

}
