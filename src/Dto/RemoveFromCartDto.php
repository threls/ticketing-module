<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class RemoveFromCartDto extends IdentifiableCartData
{
    public function __construct(
        #[RequiredWithout(['user_id', 'session_id'])]
        ?int $cartId,
        #[RequiredWithout(['user_id', 'cart_id'])]
        ?string $sessionId,
        #[RequiredWithout(['session_id', 'cart_id'])]
        ?int $userId,
        public int $cartItemId,
    ) {
        parent::__construct($cartId, $sessionId, $userId);
    }
}
