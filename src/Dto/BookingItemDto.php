<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class BookingItemDto extends Data
{
    public function __construct(
        public ?int $bookingId,
        public int $bookableId,
        public int $bookableType,
        public int $qty,
        public int $amount,
        public string $currency,
        public int $vatAmount,
    )
    {
    }

}
