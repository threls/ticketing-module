<?php

namespace Threls\ThrelsTicketingModule\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Threls\ThrelsTicketingModule\Models\Booking;

class BookingConfirmedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Booking $booking) {}
}
