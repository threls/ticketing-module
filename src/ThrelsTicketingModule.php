<?php

namespace Threls\ThrelsTicketingModule;

use Threls\ThrelsTicketingModule\Actions\AddCustomTicketRestrictionAction;
use Threls\ThrelsTicketingModule\Actions\AddOrUpdateToCartAction;
use Threls\ThrelsTicketingModule\Actions\ClearCartAction;
use Threls\ThrelsTicketingModule\Actions\CreateBookingAction;
use Threls\ThrelsTicketingModule\Actions\CreateEventAction;
use Threls\ThrelsTicketingModule\Actions\CreateTicketAction;
use Threls\ThrelsTicketingModule\Actions\DeleteCartAction;
use Threls\ThrelsTicketingModule\Actions\DeleteCustomRestrictionAction;
use Threls\ThrelsTicketingModule\Actions\DeleteEventAction;
use Threls\ThrelsTicketingModule\Actions\DeleteTicketAction;
use Threls\ThrelsTicketingModule\Actions\GenerateBookingQRCodesAction;
use Threls\ThrelsTicketingModule\Actions\GenerateTicketPDFsAction;
use Threls\ThrelsTicketingModule\Actions\GetCartAction;
use Threls\ThrelsTicketingModule\Actions\RemoveFromCartAction;
use Threls\ThrelsTicketingModule\Actions\UpdateBookingStatus;
use Threls\ThrelsTicketingModule\Actions\UpdateCustomTicketRestrictionAction;
use Threls\ThrelsTicketingModule\Actions\UpdateEventAction;
use Threls\ThrelsTicketingModule\Actions\UpdateTicketAction;
use Threls\ThrelsTicketingModule\Dto\AddCustomTicketRestrictionDto;
use Threls\ThrelsTicketingModule\Dto\AddOrUpdateToCartDto;
use Threls\ThrelsTicketingModule\Dto\CartDto;
use Threls\ThrelsTicketingModule\Dto\CreateBookingDto;
use Threls\ThrelsTicketingModule\Dto\CreateEventDto;
use Threls\ThrelsTicketingModule\Dto\CreateTicketDto;
use Threls\ThrelsTicketingModule\Dto\IdentifiableCartData;
use Threls\ThrelsTicketingModule\Dto\RemoveFromCartDto;
use Threls\ThrelsTicketingModule\Dto\UpdateCustomTicketRestrictionDto;
use Threls\ThrelsTicketingModule\Dto\UpdateEventDto;
use Threls\ThrelsTicketingModule\Dto\UpdateTicketDto;
use Threls\ThrelsTicketingModule\Enums\BookingStatusEnum;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\Event;
use Threls\ThrelsTicketingModule\Models\Ticket;

class ThrelsTicketingModule
{
    public function createEvent(CreateEventDto $dto): Event
    {
        return app(CreateEventAction::class)->execute($dto);
    }

    public function createTicket(CreateTicketDto $dto): Ticket
    {
        return app(CreateTicketAction::class)->execute($dto);
    }

    public function addOrUpdateCart(AddOrUpdateToCartDto $dto): CartDto
    {
        return app(AddOrUpdateToCartAction::class)->execute($dto);
    }

    public function removeFromCart(RemoveFromCartDto $dto): CartDto
    {
        return app(RemoveFromCartAction::class)->execute($dto);
    }

    public function getCart(IdentifiableCartData $dto): CartDto
    {
        return app(GetCartAction::class)->execute($dto);
    }

    public function clearCart(IdentifiableCartData $dto): CartDto
    {
        return app(ClearCartAction::class)->execute($dto);
    }

    public function updateEvent(UpdateEventDto $dto): Event
    {
        return app(UpdateEventAction::class)->execute($dto);
    }

    public function updateTicket(UpdateTicketDto $dto): Ticket
    {
        return app(UpdateTicketAction::class)->execute($dto);
    }

    public function deleteEvent(int $eventId): void
    {
        app(DeleteEventAction::class)->execute($eventId);
    }

    public function deleteTicket(int $ticketId): void
    {
        app(DeleteTicketAction::class)->execute($ticketId);
    }

    public function deleteCart(int $cartId): void
    {
        app(DeleteCartAction::class)->execute($cartId);
    }

    public function createBooking(CreateBookingDto $dto): Booking
    {
        return app(CreateBookingAction::class)->execute($dto);
    }

    public function updateBookingStatus(Booking $booking, BookingStatusEnum $bookingStatus): void
    {
        app(UpdateBookingStatus::class)->execute($booking, $bookingStatus);
    }

    public function addCustomTicketRestrictions(AddCustomTicketRestrictionDto $dto): void
    {
        app(AddCustomTicketRestrictionAction::class)->execute($dto);
    }

    public function updateCustomTicketRestrictions(UpdateCustomTicketRestrictionDto $dto): void
    {
        app(UpdateCustomTicketRestrictionAction::class)->execute($dto);
    }

    public function deleteCustomTicketRestriction(int $customRestrictionId): void
    {
        app(DeleteCustomRestrictionAction::class)->execute($customRestrictionId);
    }

    public function generateBookingQRCodes(Booking $booking): void
    {
        app(GenerateBookingQRCodesAction::class)->execute($booking);
    }

    public function generateTicketPdfs(Booking $booking): void
    {
        app(GenerateTicketPdfsAction::class)->execute($booking);
    }
}
