<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingItem;
use Threls\ThrelsTicketingModule\Models\Ticket;

#[MapName(SnakeCaseMapper::class)]
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
