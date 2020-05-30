<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine;

use App\Transport\Prague\Statistic\TripList\TripListCacheService;
use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use App\Transport\Prague\StopLine\Trip\TripRepository;
use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\Utils\DatetimeFactory;

class StopLineFactory
{
	private StopTimeRepository $stopTimeRepository;

	private TripRepository $tripRepository;

	private VehiclePositionRepository $vehiclePositionRepository;

	private DatetimeFactory $datetimeFactory;

	private TripListCacheService $tripListCacheService;

	public function __construct(
		StopTimeRepository $stopTimeRepository,
		TripRepository $tripRepository,
		VehiclePositionRepository $vehiclePositionRepository,
		DatetimeFactory $datetimeFactory,
		TripListCacheService $tripListCacheService
	) {
		$this->stopTimeRepository = $stopTimeRepository;
		$this->tripRepository = $tripRepository;
		$this->vehiclePositionRepository = $vehiclePositionRepository;
		$this->datetimeFactory = $datetimeFactory;
		$this->tripListCacheService = $tripListCacheService;
	}

	/**
	 * @return StopLine[]
	 */
	public function getStopLinesForStop(Stop $stop, int $limit = 10): array
	{
		$stopTimes = $this->stopTimeRepository->findForDepartureTable(
			$stop->getId(),
			$this->datetimeFactory->createNow()
		);
		$trips = $this->tripRepository->findForDepartureTable($stop->getId(), $this->datetimeFactory->createToday());

		$lastVehiclePosition = $this->vehiclePositionRepository->findLast();
		$vehicles = [];
		if ($lastVehiclePosition !== null) {
			$vehicles = $lastVehiclePosition->getVehicles();
		}

		$index = 0;
		$stopLines = [];
		foreach ($stopTimes as $stopTime) {
			if (array_key_exists($stopTime->getDateTripId(), $trips)) {
				$trip = $trips[$stopTime->getDateTripId()];
			} else {
				continue;
			}

			$vehicle = null;
			if (array_key_exists($stopTime->getDateTripId(), $vehicles)) {
				$vehicle = $vehicles[$stopTime->getDateTripId()];
			}

			$stopLine = new StopLine(
				$stop,
				$stopTime->getArrivalTime(),
				$stopTime->getDepartureTime(),
				$stopTime->getTripId(),
				$trip->getLineNumber(),
				$trip->getTripHeadsign(),
				$vehicle,
				$this->datetimeFactory->createNow(),
				$this->tripListCacheService->hasTripList($stopTime->getTripId())
			);

			if ($stopLine->hasVehicleLeft()) {
				continue;
			}

			$stopLines[] = $stopLine;

			$index++;
			if ($index > $limit) {
				break;
			}
		}

		return $stopLines;
	}
}
