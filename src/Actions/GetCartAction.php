<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\CartDto;
use Threls\ThrelsTicketingModule\Dto\IdentifiableCartData;
use Threls\ThrelsTicketingModule\Exceptions\CartNotFoundException;

class GetCartAction
{
    public function __construct(
        protected readonly GetCartByIdentifiableAction $getCartByIdentifiableAction
    ) {}

    public function execute(IdentifiableCartData $identifiableCartData)
    {
        $cart = $this->getCartByIdentifiableAction->execute($identifiableCartData);

        if (! $cart) {
            throw new CartNotFoundException;
        }

        return CartDto::from($cart);

    }
}
