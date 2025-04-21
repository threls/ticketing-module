<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CartDto extends Data
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $sessionId,
        #[DataCollectionOf(CartItemDto::class)]
        public Collection $items,
    ) {}
}
