<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use App\UI\Shared\Map\IMapObjectProvider;
use App\UI\Shared\Map\MapObject;

class VehicleMapObjectProvider implements IMapObjectProvider
{
    /** @var VehiclePositionRepository */
    private $vehiclePositionRepository;

    public function __construct(VehiclePositionRepository $vehiclePositionRepository)
    {
        $this->vehiclePositionRepository = $vehiclePositionRepository;
    }

    /**
     * @return MapObject[]
     */
    public function getMapObjects(): array
    {
        $vehiclePosition = $this->vehiclePositionRepository->findLast();
        if ($vehiclePosition === null) {
            return [];
        }

        $objects = [];
        foreach ($vehiclePosition->getVehicles() as $vehicle) {
            $objects[] = new MapObject(
                $vehicle->getCoordinates(),
                $vehicle->getMapLabel()
            );
        }

        return $objects;
    }
}
