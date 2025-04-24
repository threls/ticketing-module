<?php

namespace Threls\ThrelsTicketingModule\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Threls\ThrelsTicketingModule\Actions\GenerateBookingQRCodesAction;
use Threls\ThrelsTicketingModule\Events\BookingConfirmedEvent;
use Threls\ThrelsTicketingModule\Jobs\GenerateTicketPDFsJob;

class GenerateBookingTicketsListener implements ShouldQueue
{
    public function __construct() {}

    public function handle(BookingConfirmedEvent $event): void
    {
        app(GenerateBookingQRCodesAction::class)->execute($event->booking);

        GenerateTicketPDFsJob::dispatch($event->booking->fresh());

    }
}
