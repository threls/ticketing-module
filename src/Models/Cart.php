<?php

namespace Threls\ThrelsTicketingModule\Models;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

/**
 * @property Money|null $total
 * @property Money|null $subtotal
 * @property Money|null $vat
 */
class Cart extends \Binafy\LaravelCart\Models\Cart
{
    use SoftDeletes;

    public $casts = [
        'extra_attributes' => SchemalessAttributes::class,
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeWithExtraAttributes(): Builder
    {
        return $this->extra_attributes->modelScope();
    }

    public function getTotalAttribute(): Money
    {
        return Money::of($this->extra_attributes->total['amount'], $this->extra_attributes->total['currency']);

    }

    public function getSubtotalAttribute(): Money
    {
        return Money::of($this->extra_attributes->subtotal['amount'], $this->extra_attributes->subtotal['currency']);

    }

    public function getVatAttribute(): Money
    {
        return Money::of($this->extra_attributes->vat_amount['amount'] ?? 0, $this->extra_attributes->vat_amount['currency']);

    }
}
