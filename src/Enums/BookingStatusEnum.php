<?php

namespace Threls\ThrelsTicketingModule\Enums;

enum BookingStatusEnum: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case REFUNDED = 'REFUNDED';

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::CANCELLED, self::REFUNDED],
            default => [],
        };
    }

    public function canTransitionTo(self $newState): bool
    {
        return in_array($newState, $this->allowedTransitions(), true);
    }
}
