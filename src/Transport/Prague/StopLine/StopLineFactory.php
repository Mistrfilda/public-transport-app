<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine;

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use App\Transport\Prague\StopLine\Trip\TripRepository;
use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\Utils\DatetimeFactory;

class StopLineFactory
{
    /** @var StopTimeRepository */
    private $stopTimeRepository;

    /** @var TripRepository */
    private $tripRepository;

    /** @var VehiclePositionRepository */
    private $vehiclePositionRepository;

    /** @var DatetimeFactory */
    private $datetimeFactory;

    public function __construct(
        StopTimeRepository $stopTimeRepository,
        TripRepository $tripRepository,
        VehiclePositionRepository $vehiclePositionRepository,
        DatetimeFactory $datetimeFactory
    ) {
        $this->stopTimeRepository = $stopTimeRepository;
        $this->tripRepository = $tripRepository;
        $this->vehiclePositionRepository = $vehiclePositionRepository;
        $this->datetimeFactory = $datetimeFactory;
    }

    /**
     * @return StopLine[]
     */
    public function getStopLinesForStop(Stop $stop): array
    {
        $stopTimes = $this->stopTimeRepository->findForDepartureTable($stop->getId(), $this->datetimeFactory->createNow());
        $trips = $this->tripRepository->findForDepartureTable($stop->getId(), $this->datetimeFactory->createToday());

        $lastVehiclePosition = $this->vehiclePositionRepository->findLast();
        $vehicles = $lastVehiclePosition->getVehicles();

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

            $stopLines[] = new StopLine(
                $stop,
                $stopTime->getArrivalTime(),
                $stopTime->getDepartureTime(),
                $stopTime->getTripId(),
                $trip->getLineNumber(),
                $trip->getTripHeadsign(),
                $vehicle
            );
        }

        return $stopLines;
    }
}
