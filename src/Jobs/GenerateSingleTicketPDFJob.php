<?php

namespace Threls\ThrelsTicketingModule\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;
use Threls\ThrelsTicketingModule\Dto\GenerateTicketPdfDto;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingTicket;

class GenerateSingleTicketPDFJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public readonly Booking $booking, public readonly BookingTicket $ticket) {}

    public function handle(): void
    {
        $dto = new GenerateTicketPdfDto(
            eventName: $this->ticket->bookingItem->event->name,
            booking: $this->booking,
            ticketNumber: $this->ticket->ticket_number,
            ticket: $this->ticket->ticket,
            item: $this->ticket->bookingItem,
            qrCode: $this->ticket->hasMedia(BookingTicket::MEDIA_QR_CODE) ? $this->ticket->getFirstMedia(BookingTicket::MEDIA_QR_CODE) : null,
            userName: $this->booking->bookingClient->full_name
        );

        $pdfBuilder = Pdf::withBrowsershot(function (Browsershot $browsershot) {
            if (! app()->environment('local')) {
                $browsershot
                    ->setChromePath(config('ticketing-module.chrome_path')) // Use manually installed Chromium
                    ->setCustomTempPath(storage_path('temp'))    // Custom temp directory for server compatibility
                    ->setOption('executablePath', config('ticketing-module.chrome_path'))
                    ->setOption('args', ['--no-sandbox'])                       // Disable sandbox for headless Chromium compatibility
                    ->newHeadless();
            } else {
                $browsershot->setNodeBinary(config('ticketing-module.node_binary_path', '/opt/homebrew/bin/node'));
            }
        });

        $pdf = $pdfBuilder->view('ticketing-module::pdf.ticket-template', $dto->toArray());

        $this->ticket->addMediaFromBase64($pdf->base64())->setFileName($this->ticket->ticket_number.'.pdf')->toMediaCollection(BookingTicket::MEDIA_TICKET);
    }
}
