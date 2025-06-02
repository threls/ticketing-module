<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingItem;
use Threls\ThrelsTicketingModule\Models\Ticket;

class GenerateTicketPdfDto extends Data
{
    public function __construct(
        public string $eventName,
        public Booking $booking,
        public Ticket $ticket,
        public BookingItem $item,
        public Media $qrCode,
        public string $userName
    ) {}
}
