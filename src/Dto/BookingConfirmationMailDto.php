<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class BookingConfirmationMailDto extends Data
{
    public function __construct(
        public string $userName,
        public int $bookingId,
        public Carbon $bookingDate,
        public Collection $attachments,
    ) {}
}
