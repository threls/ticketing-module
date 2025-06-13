<?php

namespace Threls\ThrelsTicketingModule\Models;

use Binafy\LaravelCart\Models\CartItem;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Cart extends \Binafy\LaravelCart\Models\Cart
{
    public $casts = [
        'extra_attributes' => SchemalessAttributes::class,
    ];

    public function scopeWithExtraAttributes(): Builder
    {
        return $this->extra_attributes->modelScope();
    }

    public function getTotalAttribute(): Money
    {
        return $this->items->reduce(
            fn(Money $total, CartItem $cartItem) =>
            $total->plus($cartItem->itemable->price->multipliedBy($cartItem->quantity)),
            Money::of(0, 'EUR')
        );
    }
}
