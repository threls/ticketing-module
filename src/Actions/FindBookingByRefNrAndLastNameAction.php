<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Models\Booking;

class FindBookingByRefNrAndLastNameAction
{
    public function execute(string $referenceNr, string $lastName)
    {
        return Booking::with('bookingClient')
            ->where('reference_nr', $referenceNr)
            ->whereHas('bookingClient', function ($query) use ($lastName) {
                $query->where('last_name', $lastName);
            })
            ->firstOrFail();
    }
}
