<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Illuminate\Support\Str;
use Threls\ThrelsTicketingModule\Dto\UpdateEventDto;
use Threls\ThrelsTicketingModule\Models\Event;

class UpdateEventAction
{
    public function execute(UpdateEventDto $dto): Event
    {
        $dto->event->update([
            'name' => $dto->name,
            'slug' => Str::slug($dto->name),
            'description' => $dto->description,
            'status' => $dto->status,
        ]);

        return $dto->event->fresh();
    }
}
