<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\Validation\RequiredWithout;

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
    )
    {
        parent::__construct($cartId, $sessionId, $userId);
    }

}
