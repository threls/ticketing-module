<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Threls\ThrelsTicketingModule\Enums\BookingStatusEnum;
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

    protected int $quantity;

    public function execute(Ticket $ticket, Carbon $date, int $quantity = 0)
    {
        $this->ticket = $ticket;
        $this->date = $date;
        $this->quantity = $quantity;

        $this->restrictions = $this->getRestrictions();

        $this->countExistingTickets()
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
            return $customRestriction->customRestrictionItems;
        } else {
            return $this->ticket->restrictions;
        }

    }

    protected function countExistingTickets(): static
    {
        $this->ticketsNr = BookingItem::query()
            ->whereHas('booking', function ($query) {
                $query->whereDate('date', $this->date)
                    ->whereIn('status', [BookingStatusEnum::CONFIRMED, BookingStatusEnum::PENDING]);
            })
            ->where('ticket_id', $this->ticket->id)
            ->selectRaw('SUM(pax_number * qty) as total')
            ->value('total');

        return $this;
    }

    protected function checkLimits(): void
    {
        $dayNumber = $this->date->day;

        $totalNr = $this->ticketsNr + $this->quantity;

        $this->restrictions->each(function (CustomRestrictionItem|TicketRestriction $restriction) use ($dayNumber, $totalNr) {
            if ($restriction->key->getDayFromRestrictionName() == $dayNumber) {
                if ($totalNr == (int) $restriction->value) {
                    throw new BadRequestHttpException('Daily limit reached for '.$this->ticket->name.' ticket.');
                }
            }
        });

    }
}
