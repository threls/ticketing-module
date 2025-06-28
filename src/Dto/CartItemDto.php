<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Binafy\LaravelCart\Models\CartItem;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Threls\ThrelsTicketingModule\Contracts\Cartable;

#[MapName(SnakeCaseMapper::class)]
class CartItemDto extends Data
{
    public function __construct(
        public int $id,
        public Cartable $item,
        public int $quantity,
    ) {}

    public static function fromModel(CartItem $cartItem): self
    {

        return new self(
            id: $cartItem->id,
            item: $cartItem->itemable,
            quantity: $cartItem->quantity
        );
    }
}
