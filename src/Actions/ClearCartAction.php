<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\CartDto;
use Threls\ThrelsTicketingModule\Dto\IdentifiableCartData;
use Threls\ThrelsTicketingModule\Exceptions\CartNotFoundException;

class ClearCartAction
{
    public function __construct(
        protected readonly GetCartByIdentifiableAction $getCartByIdentifiableAction
    )
    {
    }

    public function execute(IdentifiableCartData $cartData): CartDto
    {
        $cart = $this->getCartByIdentifiableAction->execute($cartData);

        if (!$cart) {
            throw new CartNotFoundException;
        }

        $cart->emptyCart();

        return CartDto::from($cart);

    }
}
