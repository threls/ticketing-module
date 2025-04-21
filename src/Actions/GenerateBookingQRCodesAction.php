<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingItem;

class GenerateBookingQRCodesAction
{
    protected Booking $booking;

    public function execute(Booking $booking): void
    {
        $this->booking = $booking;

        $booking->items->each(function (BookingItem $item) use ($booking) {
            $this->generateQRCode($item);
        });
    }

    protected function generateQRCode(BookingItem $item): void
    {
        $writer = new PngWriter();

        $qrCode = new QrCode(
            data: $item->reference_number,
        );

        $result = $writer->write($qrCode);

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $result->getString());
        rewind($stream);

        $item->addMediaFromStream($stream)->toMediaCollection(BookingItem::MEDIA_QR_CODE);

        fclose($stream);

    }

}
