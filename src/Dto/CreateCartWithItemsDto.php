<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class CreateCartWithItemsDto
{
    public function __construct(
        public readonly ?string $sessionId,
        public readonly ?int $userId,
        public readonly ?array $extraAttributes,
        #[DataCollectionOf(UpdateCartItemDto::class)]
        public Collection $items,
    ) {}
}
