<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking;

use App\Transport\ParkingLot\IParkingLot;
use App\UI\Shared\Map\IMapObjectProvider;
use App\UI\Shared\Map\MapObject;

class ParkingLotMapObjectProvider implements IMapObjectProvider
{
	private const MAP_ICON = 'https://maps.google.com/mapfiles/kml/shapes/parking_lot.png';

	private ParkingLotRepository $parkingLotRepository;

	public function __construct(ParkingLotRepository $parkingLotRepository)
	{
		$this->parkingLotRepository = $parkingLotRepository;
	}

	/**
	 * @return MapObject[]
	 */
	public function getMapObjects(): array
	{
		$mapObjects = [];
		foreach ($this->parkingLotRepository->findAll() as $parkingLot) {
			$mapObjects[] = new MapObject(
				$parkingLot->getCoordinates(),
				sprintf('%s - %s', $parkingLot->getName(), ParkingType::LABELS[$parkingLot->getParkingType()]),
				self::MAP_ICON,
				$this->getParkingWindowLines($parkingLot)
			);
		}

		return $mapObjects;
	}

	/**
	 * @return string[]
	 */
	private function getParkingWindowLines(IParkingLot $parkingLot): array
	{
		return [
			sprintf('%s - %s', $parkingLot->getName(), ParkingType::LABELS[$parkingLot->getParkingType()]),
			$parkingLot->getAddress(),
			sprintf('Celkový počet míst: %s', $parkingLot->getLastParkingLotOccupancy()->getTotalSpaces()),
			sprintf('Obsazený počet míst: %s', $parkingLot->getLastParkingLotOccupancy()->getOccupiedSpaces()),
			sprintf('Volný počet míst: %s', $parkingLot->getLastParkingLotOccupancy()->getFreeSpaces()),
			'',
		];
	}
}
