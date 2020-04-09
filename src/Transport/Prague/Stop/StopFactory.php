<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop;

use Mistrfilda\Pid\Api\Stop\Stop as PIDStop;

class StopFactory
{
	public function create(
		string $name,
		string $stopId,
		float $latitude,
		float $longitude
	): Stop {
		return new Stop(
			$name,
			$stopId,
			$latitude,
			$longitude
		);
	}

	public function createFromPidLibrary(PIDStop $stop): Stop
	{
		return $this->create(
			$stop->getName(),
			$stop->getStopId(),
			$stop->getLatitude(),
			$stop->getLongitude()
		);
	}
}
