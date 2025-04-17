<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Threls\ThrelsTicketingModule\Enums\TicketStatusEnum;
use Threls\ThrelsTicketingModule\Models\Ticket;

#[MapName(SnakeCaseMapper::class)]
class UpdateTicketDto extends Data
{
    public function __construct(
        public Ticket $ticket,
        public ?int $dependentOfId,
        public string $name,
        public int $paxNumber,
        public int $price,
        public string $currency,
        public int $vatId,
        public TicketStatusEnum $status,
        #[DataCollectionOf(TicketRestrictionDto::class)]
        public ?Collection $restrictions,
    ) {}
}
