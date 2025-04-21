<?php

namespace Threls\ThrelsTicketingModule;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Threls\ThrelsTicketingModule\Commands\ThrelsTicketingModuleCommand;
use Threls\ThrelsTicketingModule\Events\BookingConfirmedEvent;
use Threls\ThrelsTicketingModule\Listeners\GenerateQRCodesListener;

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
            ->hasMigration('create_ticketing_module_table')
            ->hasCommand(ThrelsTicketingModuleCommand::class);
    }

    public function packageRegistered()
    {
        $this->app['events']->listen(
            BookingConfirmedEvent::class, GenerateQRCodesListener::class
        );

    }
}
