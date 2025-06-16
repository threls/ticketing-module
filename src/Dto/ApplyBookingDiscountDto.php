<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Threls\ThrelsTicketingModule\Models\Booking;

#[MapName(SnakeCaseMapper::class)]
class ApplyBookingDiscountDto extends Data
{
    public function __construct(
        public Booking $booking,
        public int $discountableId,
        public int $discountableType,
        public int $amount,
        public string $currency,
    )
    {
    }

}
