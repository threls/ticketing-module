<?php

namespace Threls\ThrelsTicketingModule\Commands;

use Illuminate\Console\Command;

class ThrelsTicketingModuleCommand extends Command
{
    public $signature = 'ticketing-module';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
