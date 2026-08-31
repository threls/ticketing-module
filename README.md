# Threls Ticketing Module

[![Latest Version on Packagist](https://img.shields.io/packagist/v/threls/ticketing-module.svg?style=flat-square)](https://packagist.org/packages/threls/ticketing-module)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/threls/ticketing-module/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/threls/ticketing-module/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/threls/ticketing-module/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/threls/ticketing-module/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/threls/ticketing-module.svg?style=flat-square)](https://packagist.org/packages/threls/ticketing-module)

A Laravel package that models a full event-ticketing flow: events and their tickets, a shopping cart, bookings with client details, per-ticket capacity restrictions, discounts, QR-coded tickets, PDF ticket generation, and a booking confirmation email.

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Domain model](#domain-model)
- [Usage](#usage)
  - [Events](#events)
  - [Tickets](#tickets)
  - [Ticket restrictions & daily limits](#ticket-restrictions--daily-limits)
  - [Cart](#cart)
  - [Bookings](#bookings)
  - [Booking discounts](#booking-discounts)
  - [QR codes & PDF tickets](#qr-codes--pdf-tickets)
  - [Booking confirmation email](#booking-confirmation-email)
- [Domain events](#domain-events)
- [Overriding models](#overriding-models)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

## Requirements

- PHP ^8.4
- Laravel ^10.0 || ^11.0 || ^12.0
- A queue worker (`php artisan queue:work`) — ticket PDF generation and QR code generation run as queued jobs/listeners
- Chrome/Chromium and Node.js available on the server for PDF rendering (`spatie/laravel-pdf` + Browsershot) — see [Configuration](#configuration)

The package also pulls in `binafy/laravel-cart` (cart storage), `brick/money` (money handling), `endroid/qr-code` (QR generation), `spatie/laravel-data` (DTOs), `spatie/laravel-medialibrary` (QR/PDF file storage), `spatie/laravel-pdf` (PDF rendering) and `spatie/laravel-schemaless-attributes` (free-form cart attributes).

## Installation

Install the package via Composer:

```bash
composer require threls/ticketing-module
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="ticketing-module-migrations"
php artisan migrate
```

Publish the config file:

```bash
php artisan vendor:publish --tag="ticketing-module-config"
```

Optionally, publish the views (the PDF ticket template and the booking confirmation mail markdown):

```bash
php artisan vendor:publish --tag="ticketing-module-views"
```

## Configuration

This is the published `config/ticketing-module.php`:

```php
return [

    // base64 company logo, printed on the generated ticket PDF
    'logo' => 'data:image/png;base64,...',

    'support_email' => 'test@threls.com',

    // path to the Chrome/Chromium binary used by Browsershot to render ticket PDFs in production
    'chrome_path' => '/usr/bin/google-chrome',

    // path to the Node binary used by Browsershot to render ticket PDFs locally
    'node_binary_path' => '/opt/homebrew/bin/node',

    // model bindings — swap in your own models as long as they extend/behave like the ones below
    'models' => [
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
    ],

];
```

`chrome_path` and `node_binary_path` are only used when a ticket PDF is generated (see [QR codes & PDF tickets](#qr-codes--pdf-tickets)): in any non-`local` environment the package launches headless Chrome at `chrome_path` with `--no-sandbox`; in the `local` environment it uses the Node binary at `node_binary_path` instead. Adjust both to match your server/workstation.

You also need at least one VAT rate row in the `vat_rates` table (a plain `id` + `rate` percentage) before creating tickets, since every ticket references one.

## Domain model

```
Event
 └─ Ticket (implements Cartable, may have a parent Ticket for "add-on" style dependencies)
     ├─ TicketRestriction (fixed weekly limits, e.g. "sunday_limit" = 50)
     └─ CustomRestriction (date-ranged override of the weekly limits)
         └─ CustomRestrictionItem

Cart (from binafy/laravel-cart) ── CartItem (itemable = Ticket)

Booking
 ├─ BookingClientDetail (name, email, phone, address)
 ├─ BookingItem (one row per ticket line in the booking, snapshotted price/VAT)
 │   └─ BookingTicket × qty (one per physical ticket, holds the QR code + PDF media)
 └─ BookingDiscount (polymorphic discountable)
```

Money columns (`price`, `amount`, `vat_amount`, `original_amount`, `discount_amount`, …) are stored as integer minor units with a paired `*_currency` column and cast to `Brick\Money\Money` via `Threls\ThrelsTicketingModule\Casts\MoneyCast`.

## Usage

All operations are exposed through the `ThrelsTicketingModule` facade (or by injecting `Threls\ThrelsTicketingModule\ThrelsTicketingModule`), backed by `spatie/laravel-data` DTOs.

```php
use Threls\ThrelsTicketingModule\Facades\ThrelsTicketingModule;
```

### Events

```php
use Threls\ThrelsTicketingModule\Dto\CreateEventDto;
use Threls\ThrelsTicketingModule\Dto\UpdateEventDto;
use Threls\ThrelsTicketingModule\Enums\EventStatusEnum;

$event = ThrelsTicketingModule::createEvent(new CreateEventDto(
    name: 'Summer Festival',
    description: 'An open-air music festival.',
    status: EventStatusEnum::ACTIVE,
));

ThrelsTicketingModule::updateEvent(new UpdateEventDto(
    event: $event,
    name: 'Summer Festival 2026',
    description: 'An open-air music festival.',
    status: EventStatusEnum::ACTIVE,
));

ThrelsTicketingModule::deleteEvent($event->id);
```

### Tickets

A ticket belongs to an event, has a price and VAT rate, and can optionally depend on a "parent" ticket (e.g. a "Parking" ticket that can only be booked alongside an "Entry" ticket). Weekly capacity restrictions are attached at creation time.

```php
use Illuminate\Support\Collection;
use Threls\ThrelsTicketingModule\Dto\CreateTicketDto;
use Threls\ThrelsTicketingModule\Dto\TicketRestrictionDto;
use Threls\ThrelsTicketingModule\Enums\TicketRestrictionEnum;
use Threls\ThrelsTicketingModule\Enums\TicketStatusEnum;

$ticket = ThrelsTicketingModule::createTicket(new CreateTicketDto(
    event: $event,
    dependentOfId: null,
    name: 'General Admission',
    paxNumber: 1,
    price: 5000, // minor units, i.e. 50.00
    currency: 'EUR',
    vatId: $vatRate->id,
    status: TicketStatusEnum::ACTIVE,
    restrictions: new Collection([
        new TicketRestrictionDto(key: TicketRestrictionEnum::SATURDAY_LIMIT, value: '200'),
        new TicketRestrictionDto(key: TicketRestrictionEnum::SUNDAY_LIMIT, value: '200'),
    ]),
));

ThrelsTicketingModule::deleteTicket($ticket->id);
```

The VAT amount is derived automatically from the ticket price and the referenced `VatRate` (`ExtractVatAction`), so you only pass the gross `price`.

### Ticket restrictions & daily limits

`TicketRestriction` rows are the ticket's default weekly limits (one row per `TicketRestrictionEnum` day: `monday_limit` … `sunday_limit`). A `CustomRestriction` overrides those limits for a specific date range — useful for holidays or special events:

```php
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Threls\ThrelsTicketingModule\Dto\AddCustomTicketRestrictionDto;
use Threls\ThrelsTicketingModule\Dto\CustomTicketRestrictionItemDto;
use Threls\ThrelsTicketingModule\Enums\TicketRestrictionEnum;

ThrelsTicketingModule::addCustomTicketRestrictions(new AddCustomTicketRestrictionDto(
    ticketId: $ticket->id,
    fromDate: Carbon::parse('2026-12-24'),
    toDate: Carbon::parse('2026-12-26'),
    restrictions: new Collection([
        new CustomTicketRestrictionItemDto(
            ticketRestrictionId: $ticketRestriction->id,
            key: TicketRestrictionEnum::THURSDAY_LIMIT,
            value: '50',
        ),
    ]),
));

ThrelsTicketingModule::deleteCustomTicketRestriction($customRestriction->id);
```

Whichever restriction set applies for a given date (custom takes priority over the default weekly one), the package enforces it whenever items are added to a cart or a booking is created, by counting already `PENDING`/`CONFIRMED` bookings for that ticket and date. Exceeding the limit throws `Symfony\Component\HttpKernel\Exception\BadRequestHttpException`; booking a dependent ticket without its parent throws `Threls\ThrelsTicketingModule\Exceptions\DependentTicketException`. Pass `checkLimits: false` to `createOrUpdateCartWithItems()` / `createBooking()` to skip this validation (e.g. for admin-created bookings).

### Cart

Carts are identified by a cart id, a session id, or a user id (at least one is required) via `IdentifiableCartData`. Cart items reference any model implementing the `Cartable` contract (the shipped `Ticket` model does).

```php
use Illuminate\Support\Collection;
use Threls\ThrelsTicketingModule\Dto\AddOrUpdateToCartDto;
use Threls\ThrelsTicketingModule\Dto\CreateOrUpdateCartWithItemsDto;
use Threls\ThrelsTicketingModule\Dto\IdentifiableCartData;
use Threls\ThrelsTicketingModule\Dto\RemoveFromCartDto;
use Threls\ThrelsTicketingModule\Dto\UpdateCartItemDto;

// Replace the cart's entire contents (useful for a "review your order" step)
$cartDto = ThrelsTicketingModule::createOrUpdateCartWithItems(new CreateOrUpdateCartWithItemsDto(
    sessionId: session()->getId(),
    userId: auth()->id(),
    cartId: null,
    extraAttributes: ['date' => '2026-09-12'], // the booking date, required for limit checks
    items: new Collection([
        new UpdateCartItemDto(itemableId: $ticket->id, itemableType: $ticket::class, options: null, quantity: 2),
    ]),
));

// Add or update a single line item
ThrelsTicketingModule::addOrUpdateCart(new AddOrUpdateToCartDto(
    cartId: $cartDto->id,
    sessionId: null,
    userId: null,
    item: new UpdateCartItemDto(itemableId: $ticket->id, itemableType: $ticket::class, options: null, quantity: 1),
));

// Fetch, remove a line item from, or clear the cart
ThrelsTicketingModule::getCart(new IdentifiableCartData(cartId: $cartDto->id, sessionId: null, userId: null));

ThrelsTicketingModule::removeFromCart(new RemoveFromCartDto(
    cartId: $cartDto->id, sessionId: null, userId: null, cartItemId: $cartItem->id,
));

ThrelsTicketingModule::clearCart(new IdentifiableCartData(cartId: $cartDto->id, sessionId: null, userId: null));

ThrelsTicketingModule::deleteCart($cartDto->id);
```

Setting a line item's `quantity` to `0` removes it from the cart. `createOrUpdateCartWithItems()` additionally removes any existing cart line whose `itemableId` is not present in the given `items` collection, so it's meant to be called with the cart's full desired contents.

### Bookings

A booking is created from an existing cart: it snapshots each cart line into a `BookingItem` (price, VAT, and reference number), totals the booking, records the client's details, and deletes the cart.

```php
use Threls\ThrelsTicketingModule\Dto\BookingClientDto;
use Threls\ThrelsTicketingModule\Dto\CreateBookingDto;
use Threls\ThrelsTicketingModule\Enums\BookingStatusEnum;

$booking = ThrelsTicketingModule::createBooking(new CreateBookingDto(
    cartId: $cartDto->id,
    eventId: $event->id,
    bookingClient: new BookingClientDto(
        firstName: 'Jane',
        lastName: 'Doe',
        email: 'jane@example.com',
        phone: '555-0100',
        address: null,
    ),
    userId: auth()->id(),
    date: Carbon::parse('2026-09-12'),
    time: '19:00',
    amount: null, // recalculated from the booking's items once created
    currency: 'EUR',
    vatAmount: null,
    status: BookingStatusEnum::PENDING,
    bookingType: 'online',
    paymentMethod: 'card',
));

// Look a booking up the way a customer would, from a "manage my booking" page
$booking = ThrelsTicketingModule::findBookingByRefAndLastName($booking->reference_nr, 'Doe');
```

Booking status transitions are validated against a fixed state machine (`BookingStatusEnum::allowedTransitions()`): `PENDING → CONFIRMED|CANCELLED`, `CONFIRMED → CANCELLED|REFUNDED`, `CANCELLED → CONFIRMED`. An invalid transition throws `BadRequestHttpException`.

```php
ThrelsTicketingModule::updateBookingStatus($booking, BookingStatusEnum::CONFIRMED);
```

Transitioning a booking to `CONFIRMED` dispatches `BookingConfirmedEvent`, which the package's own listener uses to generate QR codes and PDF tickets automatically (see below) — you don't need to call those actions yourself in the normal flow.

### Booking discounts

```php
use Threls\ThrelsTicketingModule\Dto\ApplyBookingDiscountDto;

$booking = ThrelsTicketingModule::applyBookingDiscount(new ApplyBookingDiscountDto(
    booking: $booking,
    discountableId: $promoCode->id,
    discountableType: $promoCode::class,
    amount: 500, // minor units taken off the booking total
    currency: 'EUR',
    vatAfterDiscount: 750,
    email: 'jane@example.com',
));
```

This records a `BookingDiscount` (polymorphic — `discountableType`/`discountableId` can point at any model in your app, e.g. a promo code or coupon) and reduces the booking's `amount` by it, moving the pre-discount total into `original_amount`. Any of your own models can become a discount target by using the `Threls\ThrelsTicketingModule\Traits\IsDiscountable` trait, which adds a `bookingDiscounts()` relation.

### QR codes & PDF tickets

These normally run automatically when a booking is confirmed (via `GenerateBookingTicketsListener`, queued on `BookingConfirmedEvent`), but can also be triggered manually:

```php
ThrelsTicketingModule::generateBookingQRCodes($booking); // one BookingTicket + QR code per ticket unit in the booking
ThrelsTicketingModule::generateTicketPdfs($booking);     // queues one PDF-generation job per BookingTicket
```

Each `BookingTicket` gets a `qr_code` media file (PNG, encoding the ticket's id) and, once the queued jobs finish, a `ticket` media file (PDF, rendered from the `ticketing-module::pdf.ticket-template` view via `spatie/laravel-pdf`). Once every ticket in the booking has its PDF, `TicketPdfsGeneratedEvent` is dispatched — listen for it in your app to, for example, notify the customer that their tickets are ready.

PDF rendering needs a real browser: make sure `chrome_path` (production) or `node_binary_path` (local) in the config file points at a working Chrome/Node install, and that a queue worker is running to process `GenerateSingleTicketPDFJob`.

### Booking confirmation email

`Threls\ThrelsTicketingModule\Mails\BookingConfirmationMail` is a standard `Mailable` built from a `BookingConfirmationMailDto` (customer name, booking reference, booking date, and a collection of attachments — `Spatie\MediaLibrary` media objects or URLs are both accepted). It renders the published `ticketing-module::emails.booking-confirmation` markdown view. Send it from your own code once you decide a booking is ready to be confirmed to the customer, e.g.:

```php
use Illuminate\Support\Facades\Mail;
use Threls\ThrelsTicketingModule\Dto\BookingConfirmationMailDto;
use Threls\ThrelsTicketingModule\Mails\BookingConfirmationMail;

Mail::to($booking->bookingClient->email)->send(new BookingConfirmationMail(new BookingConfirmationMailDto(
    userName: $booking->bookingClient->full_name,
    bookingReference: $booking->reference_nr,
    bookingDate: $booking->date,
    attachments: $booking->bookingTickets->map(fn ($ticket) => $ticket->getFirstMedia(\Threls\ThrelsTicketingModule\Models\BookingTicket::MEDIA_TICKET)),
)));
```

## Domain events

| Event | Dispatched when | Default listener |
|---|---|---|
| `BookingConfirmedEvent` | A booking transitions to `BookingStatusEnum::CONFIRMED` | `GenerateBookingTicketsListener` — generates QR codes, then queues PDF generation |
| `TicketPdfsGeneratedEvent` | Every `BookingTicket` in a booking has finished PDF generation | none — listen for it in your app |

## Overriding models

Every model the package writes to is resolved through `TicketingModelResolverManager`, driven by the `models` key in the config file. To use your own model (e.g. to add relations or scopes), extend the shipped model and point the config at it:

```php
// config/ticketing-module.php
'models' => [
    'ticket' => \App\Models\Ticket::class, // extends Threls\ThrelsTicketingModule\Models\Ticket
    // ...
],
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sabina Haloci](https://github.com/sabina)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
