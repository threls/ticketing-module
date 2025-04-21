<?php

namespace Threls\ThrelsTicketingModule\Models;

use Binafy\LaravelCart\Cartable;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Threls\ThrelsTicketingModule\Casts\MoneyCast;

/**
 * @property Money|null $price
 * @property Money|null $vat_amount
 */
class Ticket extends Model implements Cartable
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => MoneyCast::class,
            'vat_amount' => MoneyCast::class,
        ];
    }

    protected $guarded = ['id'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function restrictions(): HasMany
    {
        return $this->hasMany(TicketRestriction::class);
    }

    public function customRestrictions(): HasMany
    {
        return $this->hasMany(CustomRestriction::class);
    }

    public function getPrice(): float
    {
        return $this->price->getAmount()->toFloat();
    }
}
