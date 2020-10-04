<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking;

use App\Utils\Datetime\DatetimeFactory;

class ParkingLotOccupancyFactory
{
	private DatetimeFactory $datetimeFactory;

	public function __construct(DatetimeFactory $datetimeFactory)
	{
		$this->datetimeFactory = $datetimeFactory;
	}

	public function create(
		ParkingLot $parkingLot,
		int $totalSpaces,
		int $freeSpaces,
		int $occupiedSpaces
	): ParkingLotOccupancy {
		return new ParkingLotOccupancy(
			$this->datetimeFactory->createNow(),
			$parkingLot,
			$totalSpaces,
			$freeSpaces,
			$occupiedSpaces
		);
	}
}
