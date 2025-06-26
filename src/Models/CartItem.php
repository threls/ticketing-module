<?php

namespace Threls\ThrelsTicketingModule\Models;

use Brick\Money\Money;

/**
 * @property Money|null $total
 * @property Money|null $price
 * @property Money|null $vat
 * @property bool $has_discount
 */
class CartItem extends \Binafy\LaravelCart\Models\CartItem
{

    public function getTotalAttribute(): Money
    {
        return Money::of($this->getOption('total'), $this->getOption('total_currency'));

    }

    public function getPriceAttribute(): Money
    {
        return Money::of($this->getOption('price'), $this->getOption('price_currency'));

    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->getOption('has_discount');

    }

    public function getVatAttribute(): ?Money
    {
        return $this->getOption('vat') ? Money::of($this->getOption('vat'), $this->getOption('vat_currency')): null;

    }

}
