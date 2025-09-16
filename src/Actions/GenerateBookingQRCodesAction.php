<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingItem;
use Threls\ThrelsTicketingModule\Models\BookingTicket;

class GenerateBookingQRCodesAction
{
    protected Booking $booking;

    public function execute(Booking $booking, bool $generateQrCodes = false): void
    {
        $this->booking = $booking;

        $booking->items->each(function (BookingItem $item) use ($generateQrCodes) {
            $this->createBookingItemTickets($item, $generateQrCodes);
        });
    }

    protected function createBookingItemTickets(BookingItem $item, bool $generateQrCodes): void
    {
        for ($qty = 1; $qty <= $item->qty; $qty++) {

            $bookingTicket = $item->bookingTickets()->create([
                'ticket_id' => $item->ticket_id,
                'ticket_type' => $item->ticket_type,
                'ticket_number' => strtoupper(uniqid($item->ticket_id)),
                'booking_id' => $item->booking_id,
            ]);

            if ($generateQrCodes) {
                $this->generateQRCode($bookingTicket);
            }
        }
    }

    protected function generateQRCode(BookingTicket $ticket): void
    {
        $writer = new PngWriter;

        $qrCode = new QrCode(
            data: $ticket->id,
        );

        $result = $writer->write($qrCode);

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $result->getString());
        rewind($stream);

        $ticket->addMediaFromStream($stream)->setFileName($ticket->ticket_number.'.png')->toMediaCollection(BookingTicket::MEDIA_QR_CODE);

        fclose($stream);

    }
}
