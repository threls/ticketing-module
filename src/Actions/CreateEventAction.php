<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Illuminate\Support\Str;
use Threls\ThrelsTicketingModule\Dto\CreateEventDto;
use Threls\ThrelsTicketingModule\Models\Event;

class CreateEventAction
{
    protected Event $event;

    protected CreateEventDto $createEventDto;

    public function execute(CreateEventDto $dto): Event
    {
        $this->createEventDto = $dto;

        $this->createEvent();

        return $this->event;

    }

    protected function createEvent()
    {
        $this->event = Event::create([
            'name' => $this->createEventDto->name,
            'slug' => Str::slug($this->createEventDto->name),
            'description' => $this->createEventDto->description,
            'status' => $this->createEventDto->status,
        ]);

    }
}
