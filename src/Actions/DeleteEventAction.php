<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Models\Event;

class DeleteEventAction
{
    public function execute(int $eventId)
    {
        $event = Event::findOrFail($eventId);

        $event->delete();

    }
}
