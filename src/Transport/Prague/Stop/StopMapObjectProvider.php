<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop;

use App\UI\Shared\Map\IMapObjectProvider;
use App\UI\Shared\Map\MapObject;

class StopMapObjectProvider implements IMapObjectProvider
{
	private const MAP_ICON = 'https://maps.google.com/mapfiles/kml/paddle/blu-circle.png';

	private StopRepository $stopRepository;

	public function __construct(StopRepository $stopRepository)
	{
		$this->stopRepository = $stopRepository;
	}

	/**
	 * @return MapObject[]
	 */
	public function getMapObjects(): array
	{
		$mapObjects = [];
		foreach ($this->stopRepository->findAll() as $stop) {
			$mapObjects[] = new MapObject(
				$stop->getCoordinates(),
				$stop->getFormattedName(),
				self::MAP_ICON,
				[
					$stop->getFormattedName(),
				]
			);
		}

		return $mapObjects;
	}
}
