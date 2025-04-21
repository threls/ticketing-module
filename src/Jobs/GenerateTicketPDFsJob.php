<?php

namespace Threls\ThrelsTicketingModule\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\LaravelPdf\Facades\Pdf;
use Threls\ThrelsTicketingModule\Dto\GenerateTicketPdfDto;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingItem;

class GenerateTicketPDFsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking)
    {
    }

    public function handle(): void
    {
        $this->booking->items->each(function (BookingItem $item) {

            $dto = new GenerateTicketPdfDto(
                eventName: $item->event->name,
                booking: $item->booking,
                ticket: $item->ticket,
                item: $item,
                qrCode: $item->getFirstMedia(BookingItem::MEDIA_QR_CODE)
            );

            $pdf = Pdf::view('pdf.ticket-template', $dto->toArray());

            $item->addMediaFromBase64($pdf->base64())->toMediaCollection(BookingItem::MEDIA_TICKET);

        });

    }
}
