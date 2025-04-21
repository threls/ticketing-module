<?php

namespace Threls\ThrelsTicketingModule\Dto;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UpdateCustomTicketRestrictionDto extends Data
{
    public function __construct(
        public int $customRestrictionId,
        public Carbon $fromDate,
        public Carbon $toDate,
        #[DataCollectionOf(CustomTicketRestrictionItemDto::class)]
        public Collection $restrictions,
    )
    {
    }

}
