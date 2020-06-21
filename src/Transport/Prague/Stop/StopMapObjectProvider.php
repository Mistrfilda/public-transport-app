<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop;

use App\UI\Shared\Map\IMapObjectProvider;
use App\UI\Shared\Map\MapObject;

class StopMapObjectProvider implements IMapObjectProvider
{
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
				[
					$stop->getFormattedName(),
				]
			);
		}

		return $mapObjects;
	}
}
