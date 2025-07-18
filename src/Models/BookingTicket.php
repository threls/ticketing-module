<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

class BookingTicket extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const string MEDIA_QR_CODE = 'qr_code';

    public const string MEDIA_TICKET = 'ticket';

    protected $guarded = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_QR_CODE)->singleFile();
        $this->addMediaCollection(self::MEDIA_TICKET)->singleFile();
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('booking'));
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('ticket'));
    }

    public function bookingItem(): BelongsTo
    {
        return $this->belongsTo(TicketingModelResolverManager::getModelClass('bookingItem'));
    }
}
