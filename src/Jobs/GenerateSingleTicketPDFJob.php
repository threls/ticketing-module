<?php

namespace Threls\ThrelsTicketingModule\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\LaravelPdf\PdfBuilder;
use Threls\ThrelsTicketingModule\Dto\GenerateTicketPdfDto;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingTicket;

class GenerateSingleTicketPDFJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking, public readonly BookingTicket $ticket, public PdfBuilder $pdfBuilder) {}

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

        $pdf = $this->pdfBuilder->view('ticketing-module::pdf.ticket-template', $dto->toArray());

        $this->ticket->addMediaFromBase64($pdf->base64())->setFileName($this->ticket->ticket_number.'.pdf')->toMediaCollection(BookingTicket::MEDIA_TICKET);

    }
}
