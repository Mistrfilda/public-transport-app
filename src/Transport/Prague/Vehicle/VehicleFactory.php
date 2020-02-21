<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use Ofce\Pid\Api\VehiclePosition\VehiclePosition as PIDVehiclePosition;

class VehicleFactory
{
    public function create(
        VehiclePosition $vehiclePosition,
        string $routeId,
        float $latitude,
        float $longitude,
        string $finalStation,
        int $delayInSeconds,
        bool $wheelchairAccessible,
        ?string $lastStopId,
        ?string $nextStopId
    ): Vehicle {
        return new Vehicle(
            $vehiclePosition,
            $routeId,
            $latitude,
            $longitude,
            $finalStation,
            $delayInSeconds,
            $wheelchairAccessible,
            $lastStopId,
            $nextStopId
        );
    }

    public function createFromPidLibrary(
        PIDVehiclePosition $pidVehiclePosition,
        VehiclePosition $vehiclePosition
    ): Vehicle {
        return $this->create(
            $vehiclePosition,
            $pidVehiclePosition->getRouteId(),
            $pidVehiclePosition->getLatitude(),
            $pidVehiclePosition->getLongitude(),
            $pidVehiclePosition->getTripHeadsign(),
            $pidVehiclePosition->getDelay(),
            $pidVehiclePosition->getWheelchairAccessible(),
            $pidVehiclePosition->getLastStopId(),
            $pidVehiclePosition->getNextStopId()
        );
    }
}
