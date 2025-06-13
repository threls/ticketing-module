<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Binafy\LaravelCart\Models\CartItem;
use Carbon\Carbon;
use Threls\ThrelsTicketingModule\Exceptions\DependentTicketException;
use Threls\ThrelsTicketingModule\Models\Cart;
use Threls\ThrelsTicketingModule\Models\Ticket;

class CheckTicketDependencyAndLimitAction
{
    public function __construct(
        protected readonly CheckTicketDailyLimitAction $checkTicketDailyLimitAction,
    ) {}

    public function execute(Cart $cart, Carbon $date): void
    {
        $itemables = collect();
        $cart->items()->each(function (CartItem $cartItem) use (&$itemables) {
            $itemables->add($cartItem->itemable());
        });

        $cart->items()->each(function (CartItem $cartItem) use ($itemables, $date) {
            /** @var Ticket $ticket */
            $ticket = $cartItem->itemable;

            if ($ticket->parent_id) {
                $parentTicket = Ticket::query()->findOrFail($ticket->parent_id);
                if (! $itemables->contains($parentTicket)) {
                    throw new DependentTicketException('You cannot book a '.$ticket->name.' ticket without a '.$parentTicket->name.' ticket.');
                }
            }

            $this->checkTicketDailyLimitAction->execute($ticket, $date);

        });
    }
}
