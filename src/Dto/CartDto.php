<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Binafy\LaravelCart\Models\Cart;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\SchemalessAttributes\SchemalessAttributes;

#[MapName(SnakeCaseMapper::class)]
class CartDto extends Data
{
    public function __construct(
        public int $id,
        public ?int $userId,
        public ?string $sessionId,
        public ?SchemalessAttributes $extraAttributes = null,
        #[DataCollectionOf(CartItemDto::class)]
        public Collection $items,
    ) {}

    public static function fromModel(Cart|\Threls\ThrelsTicketingModule\Models\Cart $cart): self
    {
        return new self(
            id: $cart->id,
            userId: $cart->user_id,
            sessionId: $cart->session_id,
            extraAttributes: !empty($cart->extra_attributes) ? $cart->extra_attributes : null,
            items: CartItemDto::collect($cart->items)
        );
    }
}
