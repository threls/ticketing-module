<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class CreateOrUpdateCartWithItemsDto extends IdentifiableCartData
{
    public function __construct(
        ?string           $sessionId,
        ?int              $userId,
        ?int              $cartId,
        public ?array     $extraAttributes,
        #[DataCollectionOf(UpdateCartItemDto::class)]
        public Collection $items,
    ) {
        parent::__construct($cartId, $sessionId, $userId);
    }
}
