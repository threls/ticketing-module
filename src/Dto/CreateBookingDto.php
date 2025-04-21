<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Threls\ThrelsTicketingModule\Enums\BookingStatusEnum;

#[MapName(SnakeCaseMapper::class)]
class CreateBookingDto extends Data
{
    public function __construct(
        public int $cartId,
        public BookingClientDto $bookingClient,
        public ?int $userId,
        public Carbon $date,
        public ?string $time,
        public ?int $amount,
        public string $currency,
        public ?int $vatAmount,
        public BookingStatusEnum $status,
    ) {}
}
