<?php

namespace Threls\ThrelsTicketingModule\Models;

use Brick\Money\Money;

/**
 * @property Money|null $total
 * @property Money|null $price
 * @property Money|null $discounted_price
 * @property Money|null $vat
 * @property bool $has_discount
 */
class CartItem extends \Binafy\LaravelCart\Models\CartItem
{
    public function getTotalAttribute(): Money
    {
        return Money::ofMinor($this->getOption('total'), $this->getOption('total_currency'));

    }

    public function getPriceAttribute(): Money
    {
        return Money::ofMinor($this->getOption('price'), $this->getOption('price_currency'));

    }

    public function getDiscountedPriceAttribute(): Money
    {
        return Money::ofMinor($this->getOption('discounted_price'), $this->getOption('discounted_price_currency'));

    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->getOption('has_discount');

    }

    public function getVatAttribute(): ?Money
    {
        return !is_null($this->getOption('vat')) ? Money::ofMinor($this->getOption('vat'), $this->getOption('vat_currency')) : null;

    }
}
