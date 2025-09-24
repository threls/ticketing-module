<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Threls\ThrelsTicketingModule\Events\TicketPdfsGeneratedEvent;
use Threls\ThrelsTicketingModule\Jobs\GenerateSingleTicketPDFJob;
use Threls\ThrelsTicketingModule\Models\Booking;
use Throwable;

class GenerateTicketPDFsAction
{
    public function execute(Booking $booking): void
    {
        Bus::batch(
            $booking->bookingTickets->map(fn ($ticket) => new GenerateSingleTicketPDFJob($booking, $ticket)
            )
        )->catch(function (Batch $batch, Throwable $e) {
            throw new BadRequestHttpException('Batch with id '.$batch->id.' did not generate tickets PDFs.');
        })->then(function () use ($booking) {
            TicketPdfsGeneratedEvent::dispatch($booking);
        })->dispatch();
    }
}
