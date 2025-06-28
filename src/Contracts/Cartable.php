<?php

namespace Threls\ThrelsTicketingModule\Contracts;

use Brick\Money\Money;

interface Cartable
{
    public function getName(): string;

    public function getPrice(): Money;

    public function getVatAmount(): Money;
}
