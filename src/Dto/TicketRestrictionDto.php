<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Threls\ThrelsTicketingModule\Enums\TicketRestrictionEnum;

#[MapName(SnakeCaseMapper::class)]
class TicketRestrictionDto extends Data
{
    public function __construct(
        public TicketRestrictionEnum $key,
        public string $value,
    ) {}
}
