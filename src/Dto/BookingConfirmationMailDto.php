<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class BookingConfirmationMailDto extends Data
{
    public function __construct(
        public string $userName,
        public string $bookingReference,
        public Carbon $bookingDate,
        public Collection $attachments,
    ) {}
}
