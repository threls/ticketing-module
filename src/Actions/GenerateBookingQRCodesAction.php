<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingItem;
use Threls\ThrelsTicketingModule\Models\BookingTicket;

class GenerateBookingQRCodesAction
{
    protected Booking $booking;

    public function execute(Booking $booking): void
    {
        $this->booking = $booking;

        $booking->items->each(function (BookingItem $item) {
            $this->createBookingItemTickets($item);
        });
    }

    protected function createBookingItemTickets(BookingItem $item): void
    {
        for ($qty = 1; $qty <= $item->qty; $qty++) {

           $bookingTicket = $item->bookingTickets()->create([
                'ticket_id' => $item->ticket_id,
                'ticket_number' => Str::uuid()->toString(),
                'booking_id' => $item->booking_id
            ]);

           $this->generateQRCode($bookingTicket);

        }
    }


    protected function generateQRCode(BookingTicket $ticket): void
    {
        $writer = new PngWriter;

        $qrCode = new QrCode(
            data: $ticket->ticket_number,
        );

        $result = $writer->write($qrCode);

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $result->getString());
        rewind($stream);

        $ticket->addMediaFromStream($stream)->toMediaCollection(BookingTicket::MEDIA_QR_CODE);

        fclose($stream);

    }
}
