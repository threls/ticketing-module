<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[MapName(SnakeCaseMapper::class)]
class BookingConfirmationMailDto extends Data
{
    public function __construct(
        public string $userName,
        public int $bookingId,
        public Carbon $bookingDate,
        public int $peopleNr,
        #[DataCollectionOf(Media::class)]
        public Collection $attachments,
    )
    {
    }

}
