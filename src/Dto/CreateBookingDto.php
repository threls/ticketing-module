<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
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
        public ?int $userId = null,
        public Carbon $date,
        public ?string $time = null,
        public ?int $amount = null,
        public string $currency,
        public ?int $vatAmount = null,
        public BookingStatusEnum $status,
    )
    {
    }

}
