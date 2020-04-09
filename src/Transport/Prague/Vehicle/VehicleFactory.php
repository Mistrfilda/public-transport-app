<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use Mistrfilda\Pid\Api\VehiclePosition\VehiclePosition as PIDVehiclePosition;

class VehicleFactory
{
	public function create(
		VehiclePosition $vehiclePosition,
		string $routeId,
		string $tripId,
		float $latitude,
		float $longitude,
		string $finalStation,
		int $delayInSeconds,
		bool $wheelchairAccessible,
		int $vehicleType,
		?string $lastStopId,
		?string $nextStopId,
		?string $registrationNumber,
		?string $company
	): Vehicle {
		return new Vehicle(
			$vehiclePosition,
			$routeId,
			$tripId,
			$latitude,
			$longitude,
			$finalStation,
			$delayInSeconds,
			$wheelchairAccessible,
			$vehicleType,
			$lastStopId,
			$nextStopId,
			$registrationNumber,
			$company
		);
	}

	public function createFromPidLibrary(
		PIDVehiclePosition $pidVehiclePosition,
		VehiclePosition $vehiclePosition
	): Vehicle {
		$vehicleType = $pidVehiclePosition->getVehicleType();
		if ($vehicleType === null) {
			$vehicleType = VehicleType::UNDEFINED;
		}

		return $this->create(
			$vehiclePosition,
			$pidVehiclePosition->getRouteId(),
			$pidVehiclePosition->getTripId(),
			$pidVehiclePosition->getLatitude(),
			$pidVehiclePosition->getLongitude(),
			$pidVehiclePosition->getTripHeadsign(),
			$pidVehiclePosition->getDelay(),
			$pidVehiclePosition->getWheelchairAccessible(),
			$vehicleType,
			$pidVehiclePosition->getLastStopId(),
			$pidVehiclePosition->getNextStopId(),
			(string) $pidVehiclePosition->getVehicleRegistrationNumber(),
			$pidVehiclePosition->getCompany()
		);
	}
}
