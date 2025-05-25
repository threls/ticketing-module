<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Binafy\LaravelCart\Models\Cart;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CartDto extends Data
{
    public function __construct(
        public int $id,
        public ?int $userId,
        public ?string $sessionId,
        #[DataCollectionOf(CartItemDto::class)]
        public Collection $items,
    ) {}

    public static function fromModel(Cart $cart): self
    {
        return new self(
            id: $cart->id,
            userId: $cart->user_id,
            sessionId: $cart->sessionId,
            items: CartItemDto::collect($cart->items)
        );
    }
}
