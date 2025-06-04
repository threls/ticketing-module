<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class BookingConfirmationMailDto extends Data
{
    public function __construct(
        public string $userName,
        public int $bookingId,
        public Carbon $bookingDate,
        public Collection $attachments,
    ) {}
}
