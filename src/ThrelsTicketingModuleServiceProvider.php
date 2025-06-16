<?php

namespace Threls\ThrelsTicketingModule;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Threls\ThrelsTicketingModule\Commands\ThrelsTicketingModuleCommand;
use Threls\ThrelsTicketingModule\Events\BookingConfirmedEvent;
use Threls\ThrelsTicketingModule\Listeners\GenerateBookingTicketsListener;

class ThrelsTicketingModuleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ticketing-module')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_carts_table',
                'create_cart_items_table',
                'create_events_table',
                'create_vat_rates_table',
                'create_tickets_table',
                'create_ticket_restrictions_table',
                'create_settings_table',
                'create_bookings_table',
                'create_booking_items_table',
                'create_custom_restrictions_table',
                'create_custom_restriction_items_table',
                'create_booking_client_details_table',
                'create_booking_tickets_table',
                'add_schemaless_attributes_to_carts_table',
                'add_reference_nr_to_bookings_table',
                'create_booking_discounts_table'
            ])
            ->hasCommand(ThrelsTicketingModuleCommand::class);
    }

    public function packageRegistered()
    {
        $this->app['events']->listen(
            BookingConfirmedEvent::class, GenerateBookingTicketsListener::class
        );

    }
}
