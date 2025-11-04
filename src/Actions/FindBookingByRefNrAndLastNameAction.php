<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Models\Booking;

class FindBookingByRefNrAndLastNameAction
{
    public function execute(string $referenceNr, string $lastName)
    {
        $booking = Booking::with('bookingClient')
            ->where('reference_nr', $referenceNr)
            ->whereHas('bookingClient', function ($query) use ($lastName) {
                $query->where('last_name', $lastName);
            })
            ->first();

        if ($booking) {
            return $booking;
        }

        return Booking::with('bookingClient')
            ->where('reference_nr', 'LIKE', '%'.$referenceNr.'%')
            ->whereHas('bookingClient', function ($query) use ($lastName) {
                $query->where('last_name', 'LIKE', '%'.$lastName.'%');
            })
            ->firstOrFail();
    }
}
