<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking;

use Mistrfilda\Pid\Api\Parking\ParkingLot\ParkingLot as PIDParkingLot;

class ParkingLotFactory
{
	public function create(
		string $parkingId,
		float $latitude,
		float $longitude,
		string $address,
		string $type,
		string $name,
		?string $paymentUrl
	): ParkingLot {
		return new ParkingLot(
			$parkingId,
			$latitude,
			$longitude,
			$address,
			$type,
			$name,
			$paymentUrl
		);
	}

	public function createFromPidLibrary(PIDParkingLot $pidParkingLot): ParkingLot
	{
		return $this->create(
			$pidParkingLot->getParkingId(),
			$pidParkingLot->getLatitude(),
			$pidParkingLot->getLongitude(),
			$pidParkingLot->getFormattedAddress(),
			(string) $pidParkingLot->getParkingTypeId(),
			$pidParkingLot->getName(),
			$pidParkingLot->getPaymentLink()
		);
	}
}
