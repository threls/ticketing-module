<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Threls\ThrelsTicketingModule\Enums\EventStatusEnum;
use Threls\ThrelsTicketingModule\Models\Event;

#[MapName(SnakeCaseMapper::class)]
class UpdateEventDto extends Data
{
    public function __construct(
        public Event $event,
        public string $name,
        public string $description,
        public EventStatusEnum $status,
    )
    {
    }

}
