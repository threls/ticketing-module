<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\CreateNewCartDto;
use Threls\ThrelsTicketingModule\Models\Cart;

class CreateNewCartAction
{
    public function execute(CreateNewCartDto $createNewCartDto): Cart
    {
        if ($createNewCartDto->sessionId == null && $createNewCartDto->userId != null) {
            $cart = Cart::query()->firstOrCreate(['user_id' => $createNewCartDto->userId]);
            $cart->extra_attributes = $createNewCartDto->extraAttributes;
            $cart->save();

            return $cart->fresh();
        } elseif($createNewCartDto->sessionId != null) {
            $cart = Cart::query()->where('session_id', $createNewCartDto->sessionId)->first();
            if (! $cart) {
                return $this->createCart($createNewCartDto);
            }
            return $cart;
        }

        return $this->createCart($createNewCartDto);

    }

    protected function createCart(CreateNewCartDto $createNewCartDto)
    {
        $cart = new Cart;
        $cart->session_id = $createNewCartDto->sessionId;
        $cart->extra_attributes = $createNewCartDto->extraAttributes;
        $cart->save();

        return $cart->fresh();
    }
}
