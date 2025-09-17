<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Threls\ThrelsTicketingModule\Casts\MoneyCast;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

class BookingItem extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => MoneyCast::class,
            'total_amount' => MoneyCast::class,
            'vat_amount' => MoneyCast::class,
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('booking'));
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('event'));
    }

    public function ticket(): MorphTo
    {
        return $this->morphTo();
    }

    public function bookingTickets(): HasMany
    {
        return $this->hasMany(TicketingModelResolverManager::getModelClass('bookingTicket'));
    }

    public function vatRate(): BelongsTo
    {
        return $this->belongsTo(VatRate::class, 'vat_id');
    }
}
