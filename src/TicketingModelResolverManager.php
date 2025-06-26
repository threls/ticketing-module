<?php

namespace Threls\ThrelsTicketingModule;

use Binafy\LaravelCart\Models\CartItem;
use Threls\ThrelsTicketingModule\Models\Booking;
use Threls\ThrelsTicketingModule\Models\BookingClientDetail;
use Threls\ThrelsTicketingModule\Models\BookingDiscount;
use Threls\ThrelsTicketingModule\Models\BookingItem;
use Threls\ThrelsTicketingModule\Models\BookingTicket;
use Threls\ThrelsTicketingModule\Models\Cart;
use Threls\ThrelsTicketingModule\Models\CustomRestriction;
use Threls\ThrelsTicketingModule\Models\CustomRestrictionItem;
use Threls\ThrelsTicketingModule\Models\Event;
use Threls\ThrelsTicketingModule\Models\Ticket;
use Threls\ThrelsTicketingModule\Models\TicketRestriction;

class TicketingModelResolverManager
{
    protected static array $models = [];

    protected static array $defaultModels = [
        'ticket' => Ticket::class,
        'ticketRestriction' => TicketRestriction::class,
        'customRestriction' => CustomRestriction::class,
        'customRestrictionItem' => CustomRestrictionItem::class,
        'booking' => Booking::class,
        'bookingTicket' => BookingTicket::class,
        'bookingDiscount' => BookingDiscount::class,
        'bookingClientDetail' => BookingClientDetail::class,
        'bookingItem' => BookingItem::class,
        'cart' => Cart::class,
        'cartItem' => CartItem::class,
        'event' => Event::class,
    ];

    public static function resolveModels(): void
    {
        static::$models = config('ticketing-module.models', static::$defaultModels);
    }

    public static function useModel(string $name, string $class): void
    {
        static::$models[$name] = $class;
    }

    public static function getModelClass(string $name): string
    {
        return static::$models[$name] ?? throw new \InvalidArgumentException("Model [$name] is not registered.");
    }

    public static function newModel(string $name)
    {
        $modelClass = static::getModelClass($name);

        return new $modelClass;
    }
}
