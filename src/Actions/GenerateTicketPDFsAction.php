<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Jobs\GenerateTicketPDFsJob;
use Threls\ThrelsTicketingModule\Models\Booking;

class GenerateTicketPDFsAction
{
    public function execute(Booking $booking): void
    {
        GenerateTicketPDFsJob::dispatch($booking);
    }
}
