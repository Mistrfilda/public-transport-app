<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\Trip;

use App\Transport\Prague\Stop\Stop;
use DateTimeImmutable;
use Ofce\Pid\Api\Trip\Trip as PIDTrip;

class TripFactory
{
    public function create(
        Stop $stop,
        string $serviceId,
        string $tripId,
        string $tripHeadsign,
        bool $wheelchairAccessible,
        DateTimeImmutable $date
    ): Trip {
        return new Trip(
            $stop,
            $serviceId,
            $tripId,
            $tripHeadsign,
            $wheelchairAccessible,
            $date
        );
    }

    public function createFromPidLibrary(
        PIDTrip $trip,
        Stop $stop,
        DateTimeImmutable $date
    ): Trip {
        return $this->create(
            $stop,
            $trip->getServiceId(),
            $trip->getTripId(),
            $trip->getTripHeadsign(),
            $trip->isWheelchairAccessible(),
            $date
        );
    }
}
