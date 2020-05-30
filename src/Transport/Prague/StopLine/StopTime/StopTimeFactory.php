<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime;

use App\Transport\Prague\Stop\Stop;
use DateTimeImmutable;
use Mistrfilda\Pid\Api\StopTime\StopTime as PIDStopTime;

class StopTimeFactory
{
	private StopTimeTimeFactory $stopTimeTimeFactory;

	public function __construct(StopTimeTimeFactory $stopTimeTimeFactory)
	{
		$this->stopTimeTimeFactory = $stopTimeTimeFactory;
	}

	public function create(
		Stop $stop,
		string $arrivalTime,
		string $departureTime,
		DateTimeImmutable $date,
		string $tripId,
		int $stopSequence
	): StopTime {
		return new StopTime(
			$stop,
			$this->stopTimeTimeFactory->createDatetime($date, $arrivalTime),
			$this->stopTimeTimeFactory->createDatetime($date, $departureTime),
			$date,
			$tripId,
			$stopSequence
		);
	}

	public function createFromPidLibrary(
		PIDStopTime $stopTime,
		Stop $stop,
		DateTimeImmutable $date
	): StopTime {
		return $this->create(
			$stop,
			$stopTime->getArivalTime(),
			$stopTime->getDepartureTime(),
			$date,
			$stopTime->getTripId(),
			$stopTime->getStopSequence()
		);
	}

	public function update(
		StopTime $stopTime,
		string $arrivalTime,
		string $departureTime,
		DateTimeImmutable $date,
		int $stopSequence
	): void {
		$stopTime->updateStopTime(
			$this->stopTimeTimeFactory->createDatetime($date, $arrivalTime),
			$this->stopTimeTimeFactory->createDatetime($date, $departureTime),
			$stopSequence
		);
	}
}
