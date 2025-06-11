<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Threls\ThrelsTicketingModule\Actions\CreateNewCartAction;

#[MapName(SnakeCaseMapper::class)]
class CreateNewCartDto extends Data
{
    public function __construct(
        public readonly ?string $sessionId,
        public readonly ?int $userId,
        public readonly ?array $extraAttributes = null,
    ) {}
}
