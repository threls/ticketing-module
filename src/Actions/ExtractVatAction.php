<?php

namespace Threls\ThrelsTicketingModule\Actions;

class ExtractVatAction
{
    public function execute(int $totalPrice, int $vatRate):int
    {
        return (int)round($totalPrice * $vatRate / (100 + $vatRate));
    }

}
