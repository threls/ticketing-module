<?php

namespace Threls\ThrelsTicketingModule\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Threls\ThrelsTicketingModule\Actions\GenerateBookingQRCodesAction;
use Threls\ThrelsTicketingModule\Actions\GenerateTicketPDFsAction;
use Threls\ThrelsTicketingModule\Events\BookingConfirmedEvent;

class GenerateBookingTicketsListener implements ShouldQueue
{
    public function __construct() {}

    public function handle(BookingConfirmedEvent $event): void
    {
        app(GenerateBookingQRCodesAction::class)->execute($event->booking);
        app(GenerateTicketPDFsAction::class)->execute($event->booking);

    }
}
