<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Threls\ThrelsTicketingModule\Contracts\Cartable;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingItem;

class GenerateTicketPdfDto extends Data
{
    public function __construct(
        public string $eventName,
        public Booking $booking,
        public string $ticketNumber,
        public Cartable $ticket,
        public BookingItem $item,
        public ?Media $qrCode,
        public string $userName
    ) {}
}
