<?php

namespace Threls\ThrelsTicketingModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Threls\ThrelsTicketingModule\TicketingModelResolverManager;

class Event extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function tickets(): HasMany
    {
        return $this->hasMany(TicketingModelResolverManager::getModelClass('ticket'));
    }
}
