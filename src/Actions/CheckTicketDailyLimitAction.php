<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Threls\ThrelsTicketingModule\Models\BookingItem;
use Threls\ThrelsTicketingModule\Models\CustomRestrictionItem;
use Threls\ThrelsTicketingModule\Models\Ticket;
use Threls\ThrelsTicketingModule\Models\TicketRestriction;

class CheckTicketDailyLimitAction
{
    protected Ticket $ticket;

    protected Carbon $date;

    protected ?int $ticketsNr;

    protected Collection $restrictions;

    public function execute(Ticket $ticket, Carbon $date)
    {
        $this->ticket = $ticket;
        $this->date = $date;

        $this->restrictions = $this->getRestrictions();

        $this->countTickets()
            ->checkLimits();

    }

    /** @return  Collection<TicketRestriction|CustomRestrictionItem> */
    protected function getRestrictions(): Collection
    {
        $customRestriction = $this->ticket->customRestrictions()
            ->whereDate('from_date', '<=', $this->date)
            ->whereDate('to_date', '>=', $this->date)
            ->first();
        if ($customRestriction) {
            return $customRestriction->customRestrictonItems;
        } else {
            return $this->ticket->restrictions;
        }

    }

    protected function countTickets(): static
    {
        $this->ticketsNr = BookingItem::query()
            ->whereHas('booking', function ($query) {
                $query->whereDate('date', $this->date);
            })
            ->where('ticket_id', $this->ticket->id)
            ->selectRaw('SUM(pax_number * qty) as total')
            ->value('total');

        return $this;
    }

    protected function checkLimits(): void
    {
        $dayNumber = $this->date->day;

        $this->restrictions->each(function (TicketRestriction $restriction) use ($dayNumber) {
            if ($restriction->key->getDayFromRestrictionName() == $dayNumber) {
                if ($this->ticketsNr == (int) $restriction->value) {
                    throw new BadRequestHttpException('Daily limit reached for '.$this->ticket->name.' ticket.');
                }
            }
        });

    }
}
