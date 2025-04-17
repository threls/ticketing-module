<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Binafy\LaravelCart\Models\Cart;
use Threls\ThrelsTicketingModule\Dto\CreateNewCartDto;

class CreateNewCartAction
{

    public function execute(CreateNewCartDto $createNewCartDto): Cart
    {
        if($createNewCartDto->sessionId !== null){
            return Cart::query()->firstOrCreate(['user_id' => $createNewCartDto->userId]);
        }
        else
        {
            $cart = Cart::query()->where('session_id', $createNewCartDto->sessionId)->first();
            if (! $cart){
               $cart = new Cart();
               $cart->session_id = $createNewCartDto->sessionId;
               $cart->save();
            }
            return $cart;
        }
    }

}
