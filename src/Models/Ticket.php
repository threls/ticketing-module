<?php

namespace Threls\ThrelsTicketingModule\Models;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Threls\ThrelsTicketingModule\Casts\MoneyCast;
use Threls\ThrelsTicketingModule\Contracts\Cartable;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

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
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('event'));
    }

    public function restrictions(): HasMany
    {
        return $this->hasMany(TicketingModelResolverManager::getModelClass('ticketRestriction'));
    }

    public function customRestrictions(): HasMany
    {
        return $this->hasMany(TicketingModelResolverManager::getModelClass('customRestriction'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function vatRate(): BelongsTo
    {
        return $this->belongsTo(VatRate::class, 'vat_id');
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVatAmount(): Money
    {
        return $this->vat_amount;
    }
}
