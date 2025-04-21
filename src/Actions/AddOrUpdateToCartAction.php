<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Binafy\LaravelCart\Models\Cart;
use Threls\ThrelsTicketingModule\Dto\AddOrUpdateToCartDto;
use Threls\ThrelsTicketingModule\Dto\CartDto;
use Threls\ThrelsTicketingModule\Dto\CreateNewCartDto;

class AddOrUpdateToCartAction
{
    protected Cart $cart;

    protected AddOrUpdateToCartDto $addToCartDto;

    public function __construct(
        protected readonly CreateNewCartAction $createNewCartAction,
        protected readonly GetCartByIdentifiableAction $getCartByIdentifiableAction
    ) {}

    public function execute(AddOrUpdateToCartDto $addToCartDto): CartDto
    {
        $this->addToCartDto = $addToCartDto;

        $this->cart = $this->getCart();

        $this->updateCartItem();

        return CartDto::from($this->cart);

    }

    protected function getCart(): Cart
    {
        $cart = $this->getCartByIdentifiableAction->execute($this->addToCartDto);

        if (! $cart) {
            $cart = $this->createNewCartAction->execute(CreateNewCartDto::from([
                'sessionId' => $this->addToCartDto->sessionId,
                'userId' => $this->addToCartDto->userId,
            ]));
        }

        return $cart;
    }

    protected function updateCartItem(): void
    {
        if ($this->addToCartDto->item->quantity == 0) {
            $this->cart->items()->where('itemable_id', $this->addToCartDto->item->itemableId)->delete();
        } else {
            $this->cart->items()->updateOrCreate(
                [
                    'itemable_id' => $this->addToCartDto->item->itemableId,
                    'itemable_type' => $this->addToCartDto->item->itemableType,
                ],
                [
                    'quantity' => $this->addToCartDto->item->quantity,
                ]
            );
        }
    }
}
