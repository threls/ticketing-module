<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Models\Ticket;

class DeleteTicketAction
{
    public function execute(int $ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);

        $ticket->restrictions()->delete();
        $ticket->delete();

    }
}
