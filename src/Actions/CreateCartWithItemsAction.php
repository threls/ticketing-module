<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\CartDto;
use Threls\ThrelsTicketingModule\Dto\CreateCartWithItemsDto;
use Threls\ThrelsTicketingModule\Dto\CreateNewCartDto;
use Threls\ThrelsTicketingModule\Dto\UpdateCartItemDto;
use Threls\ThrelsTicketingModule\Models\Cart;

class CreateCartWithItemsAction
{
    protected readonly CreateCartWithItemsDto $dto;

    protected Cart $cart;

    public function __construct(
        protected readonly CreateNewCartAction $createNewCartAction,
    ) {}

    public function execute(CreateCartWithItemsDto $dto)
    {
        $this->dto = $dto;

        $this->createCart()
            ->createCartItems();

        return CartDto::from($this->cart->fresh());

    }

    protected function createCart(): self
    {
        $this->cart = $this->createNewCartAction->execute(CreateNewCartDto::from([
            'sessionId' => $this->dto->sessionId,
            'userId' => $this->dto->userId,
            'extraAttributes' => $this->dto->extraAttributes,
        ]));

        return $this;
    }

    protected function createCartItems()
    {
        $this->dto->items->each(function (UpdateCartItemDto $dto) {
            $this->updateCartItem($dto);
        });

        return $this;
    }

    protected function updateCartItem(UpdateCartItemDto $dto): void
    {
        if ($dto->quantity == 0) {
            $this->cart->items()->where('itemable_id', $dto->itemableId)->delete();
        } else {
            $this->cart->items()->updateOrCreate(
                [
                    'itemable_id' => $dto->itemableId,
                    'itemable_type' => $dto->itemableType,
                ],
                [
                    'quantity' => $dto->quantity,
                ]
            );
        }
    }
}
