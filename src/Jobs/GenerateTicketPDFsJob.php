<?php

namespace Threls\ThrelsTicketingModule\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Threls\ThrelsTicketingModule\Dto\GenerateTicketPdfDto;
use Threls\ThrelsTicketingModule\Events\TicketPdfsGeneratedEvent;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingTicket;

class GenerateTicketPDFsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking) {}

    public function handle(): void
    {
        if ($this->booking->bookingTickets()->count() == 0) {
            $this->fail(new BadRequestHttpException('There are no tickets generated for this booking.'));
        }

        $this->booking->bookingTickets->each(function (BookingTicket $ticket) {

            $dto = new GenerateTicketPdfDto(
                eventName: $ticket->bookingItem->event->name,
                booking: $this->booking,
                ticketNumber: $ticket->ticket_number,
                ticket: $ticket->ticket,
                item: $ticket->bookingItem,
                qrCode: $ticket->hasMedia(BookingTicket::MEDIA_QR_CODE) ? $ticket->getFirstMedia(BookingTicket::MEDIA_QR_CODE) : null,
                userName: $this->booking->bookingClient->full_name
            );

            $pdf = Pdf::withBrowsershot(function (Browsershot $browsershot) {
                if (! app()->environment('local')) {
                    $browsershot
//                        ->setEnvironmentOptions(["XDG_CONFIG_HOME" => "/tmp/google-chrome-for-testing", "XDG_CACHE_HOME" => "/tmp/google-chrome-for-testing"])
                        ->setChromePath(config('ticketing-module.chrome_path')) // Use manually installed Chromium
                        ->setCustomTempPath(storage_path('temp'))    // Custom temp directory for server compatibility
                        ->setOption('executablePath', config('ticketing-module.chrome_path'))
                        ->setOption('args', ['--no-sandbox'])                       // Disable sandbox for headless Chromium compatibility
                        ->newHeadless();
                } else {
                    $browsershot->setNodeBinary('/opt/homebrew/bin/node');
                }
            })->view('ticketing-module::pdf.ticket-template', $dto->toArray());

            $ticket->addMediaFromBase64($pdf->base64())->setFileName($ticket->ticket_number.'.pdf')->toMediaCollection(BookingTicket::MEDIA_TICKET);

        });

        TicketPdfsGeneratedEvent::dispatch($this->booking);

    }
}
