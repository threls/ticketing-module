<?php

namespace Threls\ThrelsTicketingModule\Enums;

enum TicketRestrictionEnum: string
{
    case MONDAY_LIMIT = 'monday_limit';
    case TUESDAY_LIMIT = 'tuesday_limit';
    case WEDNESDAY_LIMIT = 'wednesday_limit';
    case THURSDAY_LIMIT = 'thursday_limit';
    case FRIDAY_LIMIT = 'friday_limit';
    case SATURDAY_LIMIT = 'saturday_limit';
    case SUNDAY_LIMIT = 'sunday_limit';


    public function getDayFromRestrictionName(): int
    {
        return match ($this) {
            self::MONDAY_LIMIT => 1,
            self::TUESDAY_LIMIT => 2,
            self::WEDNESDAY_LIMIT => 3,
            self::THURSDAY_LIMIT => 4,
            self::FRIDAY_LIMIT => 5,
            self::SATURDAY_LIMIT => 6,
            self::SUNDAY_LIMIT => 0,
        };

    }

}
