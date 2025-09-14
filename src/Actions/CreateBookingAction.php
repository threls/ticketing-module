<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Brick\Money\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Threls\ThrelsTicketingModule\Dto\CreateBookingDto;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\Cart;
use Threls\ThrelsTicketingModule\Models\CartItem;

class CreateBookingAction
{
    protected CreateBookingDto $dto;

    protected Booking $booking;

    protected Cart $cart;

    public function __construct(
        protected readonly DeleteCartAction $deleteCartAction,
        protected readonly CheckTicketDependencyAndLimitAction $checkTicketDependencyAndLimitAction,
    ) {}

    public function execute(CreateBookingDto $dto, bool $checkLimits = true)
    {
        $this->dto = $dto;
        $this->cart = Cart::query()->findOrFail($this->dto->cartId);

        DB::transaction(function () use ($checkLimits) {
            if ($checkLimits){
                $this->checkTicketDependencyAndLimit();
            }
            $this->createBooking()
                ->createBookingClient()
                ->createBookingItems()
                ->updateBookingTotal()
                ->deleteCart();
        });

        return $this->booking;
    }

    protected function checkTicketDependencyAndLimit(): static
    {
        $this->checkTicketDependencyAndLimitAction->execute($this->cart, $this->dto->date);

        return $this;
    }

    protected function createBooking(): static
    {
        $booking = new Booking;
        $booking->event_id = $this->dto->eventId;
        $booking->reference_nr = strtoupper(Str::random(8));
        $booking->user_id = $this->dto->userId;
        $booking->date = $this->dto->date;
        $booking->time = $this->dto->time;
        $booking->amount = $this->dto->amount;
        $booking->amount_currency = $this->dto->currency;
        $booking->original_amount = $this->dto->amount;
        $booking->original_amount_currency = $this->dto->currency;
        $booking->status = $this->dto->status;
        $booking->booking_type = $this->dto->bookingType;
        $booking->payment_method = $this->dto->paymentMethod;
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
        $this->cart->items->each(function (CartItem $item): void {

            $itemable = $item->itemable;

            $this->booking->items()->create([
                'event_id' => $itemable->event->id,
                'ticket_id' => $itemable->id,
                'qty' => $item->quantity,
                'amount' => $item->discounted_price->getMinorAmount()->toInt(),
                'amount_currency' => $itemable->price_currency,
                'total_amount' => $item->total->getMinorAmount()->toInt(),
                'total_amount_currency' => $itemable->price_currency,
                'vat_amount' => $item->vat->getMinorAmount()->toInt(),
                'vat_amount_currency' => $itemable->price_currency,
                'vat_id' => $itemable->vat_id ?? null,
                'pax_number' => $itemable->pax_number ?? null,
                'reference_number' => strtoupper(Str::substr(trim($itemable->event->name), 0, 3)).'-'.Str::ulid(),
            ]);
        });

        return $this;
    }

    protected function updateBookingTotal(): static
    {
        $totalAmount = $this->booking->items()->sum('total_amount');
        $vatAmount = $this->booking->items()->sum('vat_amount');

        $this->booking->update([
            'amount' => Money::ofMinor($totalAmount, $this->dto->currency ?? 'EUR'),
            'vat_amount' => Money::ofMinor($vatAmount, $this->dto->currency ?? 'EUR'),
        ]);

        return $this;
    }

    protected function deleteCart()
    {
        $this->deleteCartAction->execute($this->cart->id);
    }
}
