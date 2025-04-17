<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingClientDetail extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

}
