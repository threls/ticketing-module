<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CreateNewCartDto extends Data
{
    public function __construct(
        #[RequiredWithout('user_id')]
        public readonly ?string $sessionId,
        #[RequiredWithout('session_id')]
        public readonly ?int $userId,
    ) {}
}
