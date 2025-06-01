<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class AddOrUpdateToCartDto extends IdentifiableCartData
{
    public function __construct(
        ?int $cartId,
        ?string $sessionId,
        ?int $userId,
        public UpdateCartItemDto $item
    ) {
        parent::__construct($cartId, $sessionId, $userId);
    }
}
