<?php

namespace Threls\ThrelsTicketingModule\Actions;

use Threls\ThrelsTicketingModule\Models\CustomRestriction;

class DeleteCustomRestrictionAction
{
    public function execute(int $customRestrictionId): void
    {
        $restriction = CustomRestriction::findOrFail($customRestrictionId);
        $restriction->delete();

    }

}
