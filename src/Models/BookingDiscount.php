<?php

namespace Threls\ThrelsTicketingModule\Models;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Threls\ThrelsTicketingModule\Casts\MoneyCast;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

/**
 * @property Money|null $amount
 */
class BookingDiscount extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => MoneyCast::class,
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('booking'));
    }
}
