<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Binafy\LaravelCart\Models\Cart;

class DeleteCartAction
{
    public function execute(int $cartId): void
    {
        $cart = Cart::query()->findOrFail($cartId);
        $cart->items()->delete();
        $cart->delete();

    }

}
