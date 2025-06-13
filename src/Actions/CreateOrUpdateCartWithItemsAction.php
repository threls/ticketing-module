<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Threls\ThrelsTicketingModule\Dto\CartDto;
use Threls\ThrelsTicketingModule\Dto\CreateOrUpdateCartWithItemsDto;
use Threls\ThrelsTicketingModule\Dto\CreateNewCartDto;
use Threls\ThrelsTicketingModule\Dto\UpdateCartItemDto;
use Threls\ThrelsTicketingModule\Models\Cart;

class CreateOrUpdateCartWithItemsAction
{
    protected readonly CreateOrUpdateCartWithItemsDto $dto;

    protected Cart $cart;

    public function __construct(
        protected readonly CreateNewCartAction $createNewCartAction,
        protected readonly GetCartByIdentifiableAction $getCartByIdentifiableAction,
        protected readonly CheckTicketDependencyAndLimitAction $checkTicketDependencyAndLimitAction,

    ) {}

    public function execute(CreateOrUpdateCartWithItemsDto $dto)
    {
        $this->dto = $dto;

        DB::transaction(function () use ($dto) {
            $this->getOrCreateCart()
                ->createCartItems()
                ->checkTicketDependencyAndLimit();
        });

        return CartDto::from($this->cart->fresh());

    }

    protected function getOrCreateCart(): self
    {
        $cart = $this->getCartByIdentifiableAction->execute($this->dto);

        if (! $cart) {
            $this->cart = $this->createNewCartAction->execute(CreateNewCartDto::from([
                'sessionId' => $this->dto->sessionId,
                'userId' => $this->dto->userId,
                'extraAttributes' => $this->dto->extraAttributes,
            ]));
        }
        else{
            $cart->extra_attributes = $this->dto->extraAttributes;
            $cart->save();
            $this->cart = $cart;
        }

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

    protected function checkTicketDependencyAndLimit(): static
    {
        $this->checkTicketDependencyAndLimitAction->execute($this->cart->fresh(), Carbon::parse($this->dto->extraAttributes['date']));

        return $this;
    }
}
