<?php

namespace Threls\ThrelsTicketingModule\Dto;


use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Data;

class IdentifiableCartData extends Data
{
    public function __construct(
        #[RequiredWithout(['user_id', 'session_id'])]
        public readonly ?int $cartId,
        #[RequiredWithout(['user_id', 'cart_id'])]
        public readonly ?string $sessionId,
        #[RequiredWithout(['session_id', 'cart_id'])]
        public readonly ?int $userId,
    ) {
    }
}
