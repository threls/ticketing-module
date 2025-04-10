<?php

namespace Threls\ThrelsTicketingModule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Threls\ThrelsTicketingModule\ThrelsTicketingModule
 */
class ThrelsTicketingModule extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Threls\ThrelsTicketingModule\ThrelsTicketingModule::class;
    }
}
