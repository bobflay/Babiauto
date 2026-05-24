<?php

namespace App\Enums;

enum RideStatus: string
{
    case Searching = 'searching';
    case Accepted = 'accepted';
    case Arriving = 'arriving';
    case Arrived = 'arrived';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Statuses where the ride is still active and may receive driver/location updates.
     *
     * @return array<int, self>
     */
    public static function active(): array
    {
        return [self::Searching, self::Accepted, self::Arriving, self::Arrived, self::InProgress];
    }

    public function isActive(): bool
    {
        return in_array($this, self::active(), true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled], true);
    }

    /**
     * Whether a transition from this status to the given status is allowed.
     */
    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNext(), true);
    }

    /**
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Searching => [self::Accepted, self::Cancelled],
            self::Accepted => [self::Arriving, self::Cancelled],
            self::Arriving => [self::Arrived, self::Cancelled],
            self::Arrived => [self::InProgress, self::Cancelled],
            self::InProgress => [self::Completed],
            self::Completed, self::Cancelled => [],
        };
    }
}
