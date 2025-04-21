<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\CartDto;
use Threls\ThrelsTicketingModule\Dto\RemoveFromCartDto;
use Threls\ThrelsTicketingModule\Exceptions\CartItemNotFoundException;
use Threls\ThrelsTicketingModule\Exceptions\CartNotFoundException;

class RemoveFromCartAction
{
    public function __construct(
        protected readonly GetCartByIdentifiableAction $getCartByIdentifiableAction
    ) {}

    public function execute(RemoveFromCartDto $dto): CartDto
    {
        $cart = $this->getCartByIdentifiableAction->execute($dto);

        if (! $cart) {
            throw new CartNotFoundException;
        }

        $item = $cart->items()->where('id', $dto->cartItemId)->first();

        if (! $item) {
            throw new CartItemNotFoundException;
        }

        $item->delete();

        return CartDto::from($cart);

    }
}
