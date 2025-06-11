<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Dto\CreateNewCartDto;
use Threls\ThrelsTicketingModule\Models\Cart;

class CreateNewCartAction
{
    public function execute(CreateNewCartDto $createNewCartDto): Cart
    {
        if ($createNewCartDto->sessionId == null) {
            return Cart::query()->firstOrCreate(['user_id' => $createNewCartDto->userId, ['extra_attributes' => $createNewCartDto->extraAttributes]]);
        } else {
            $cart = Cart::query()->where('session_id', $createNewCartDto->sessionId)->first();
            if (! $cart) {
                $cart = new Cart;
                $cart->session_id = $createNewCartDto->sessionId;
                $cart->save();
            }

            return $cart;
        }
    }
}
