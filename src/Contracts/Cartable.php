<?php

namespace Threls\ThrelsTicketingModule\Contracts;

use Brick\Money\Money;

interface Cartable extends \Binafy\LaravelCart\Cartable
{
    public function getName(): string;

    public function getPrice(): float;

    public function getAmount(): Money;

    public function getVatAmount(): Money;
}
