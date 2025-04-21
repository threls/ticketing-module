<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Binafy\LaravelCart\Models\Cart;
use Binafy\LaravelCart\Models\CartItem;
use Illuminate\Support\Str;
use Threls\ThrelsTicketingModule\Dto\CreateBookingDto;
use Threls\ThrelsTicketingModule\Exceptions\DependentTicketException;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\Ticket;

class CreateBookingAction
{
    protected CreateBookingDto $dto;

    protected Booking $booking;

    protected Cart $cart;

    public function __construct(
        protected readonly DeleteCartAction $deleteCartAction,
        protected readonly CheckTicketDailyLimitAction $checkTicketDailyLimitAction,
    ) {}

    public function execute(CreateBookingDto $dto)
    {
        $this->dto = $dto;
        $this->cart = Cart::query()->findOrFail($this->dto->cartId);

        $this->checkTicketDependency()
            ->createBooking()
            ->createBookingClient()
            ->createBookingItems()
            ->updateBookingTotal()
            ->deleteCart();

        return $this->booking;
    }

    protected function checkTicketDependency(): static
    {
        $itemables = collect();
        $this->cart->items()->each(function (CartItem $cartItem) use (&$itemables) {
            $itemables->add($cartItem->itemable());
        });

        $this->cart->items()->each(function (CartItem $cartItem) use ($itemables) {
            /** @var Ticket $ticket */
            $ticket = $cartItem->itemable;

            if ($ticket->parent_id) {
                $parentTicket = Ticket::query()->findOrFail($ticket->parent_id);
                if (! $itemables->contains($parentTicket)) {
                    throw new DependentTicketException('You cannot book a '.$ticket->name.' ticket without a '.$parentTicket->name.' ticket.');
                }
            }

            $this->checkTicketDailyLimitAction->execute($ticket, $this->dto->date);

        });

        return $this;

    }

    protected function createBooking(): static
    {
        $booking = new Booking;
        $booking->user_id = $this->dto->userId;
        $booking->date = $this->dto->date;
        $booking->time = $this->dto->time;
        $booking->amount = $this->dto->amount;
        $booking->currency = $this->dto->currency;
        $booking->status = $this->dto->status;
        $booking->save();

        $this->booking = $booking;

        return $this;

    }

    protected function createBookingClient(): static
    {
        $this->booking->bookingClient()->create(
            [
                'first_name' => $this->dto->bookingClient->firstName,
                'last_name' => $this->dto->bookingClient->lastName,
                'email' => $this->dto->bookingClient->email,
                'phone' => $this->dto->bookingClient->phone,
                'address' => $this->dto->bookingClient->address,
            ]
        );

        return $this;
    }

    protected function createBookingItems(): static
    {
        $this->cart->items()->each(function (CartItem $item): void {

            $itemable = $item->itemable;

            $this->booking->items()->create([
                'event_id' => $itemable->event->id,
                'ticket_id' => $itemable->id,
                'qty' => $item->quantity,
                'amount' => $itemable->getPrice(),
                'currency' => $itemable->currency,
                'vat_amount' => property_exists($itemable, 'vat_amount') ? $itemable->vat_amount : null,
                'vat_id' => property_exists($itemable, 'vat_id') ? $itemable->vat_id : null,
                'pax_number' => property_exists($itemable, 'pax_number') ? $itemable->pax_number : null,
                'reference_number' => strtoupper(Str::substr(trim($itemable->event->name),0,3) ). '-'. Str::ulid()
            ]);
        });

        return $this;
    }

    protected function updateBookingTotal(): static
    {
        $totalAmount = $this->booking->items()->sum('amount');
        $vatAmount = $this->booking->items()->sum('vat_amount');

        $this->booking->update([
            'amount' => $totalAmount,
            'vat_amount' => $vatAmount,
        ]);

        return $this;
    }

    protected function deleteCart()
    {
        $this->deleteCartAction->execute($this->cart->id);
    }
}
