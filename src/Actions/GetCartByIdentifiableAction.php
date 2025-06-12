<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Models\Cart;
use Threls\ThrelsTicketingModule\Dto\IdentifiableCartData;

class GetCartByIdentifiableAction
{
    public function execute(IdentifiableCartData $cartData): ?Cart
    {
        if ($cartData->cartId) {
            $cart = Cart::query()->where('id', $cartData->cartId)->first();
        } elseif ($cartData->userId) {
            $cart = Cart::query()->where('user_id', $cartData->userId)->first();
        } elseif ($cartData->sessionId) {
            $cart = Cart::query()->where('session_id', $cartData->sessionId)->first();
        } else {
            $cart = null;
        }

        return $cart;

    }
}
