<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Threls\ThrelsTicketingModule\Enums\EventStatusEnum;

#[MapName(SnakeCaseMapper::class)]
class CreateEventDto extends Data
{
    public function __construct(
        public string $name,
        public string $description,
        public EventStatusEnum $status,
    )
    {
    }

}
