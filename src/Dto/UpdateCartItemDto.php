<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UpdateCartItemDto extends Data
{
    public function __construct(
        public readonly int $itemableId,
        public readonly string $itemableType,
        #[Min(0)]
        public readonly int $quantity,
    )
    {
    }

}
