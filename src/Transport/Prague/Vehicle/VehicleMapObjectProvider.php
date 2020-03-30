<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use App\Transport\Prague\Stop\StopCacheService;
use App\Transport\Vehicle\IVehicle;
use App\UI\Shared\Map\IMapObjectProvider;
use App\UI\Shared\Map\MapObject;
use Nette\Application\LinkGenerator;
use Nette\Utils\Html;

class VehicleMapObjectProvider implements IMapObjectProvider
{
    /** @var VehiclePositionRepository */
    private $vehiclePositionRepository;

    /** @var StopCacheService */
    private $stopCacheService;

    /** @var LinkGenerator */
    private $linkGenerator;

    public function __construct(
        VehiclePositionRepository $vehiclePositionRepository,
        StopCacheService $stopCacheService,
        LinkGenerator $linkGenerator
    ) {
        $this->vehiclePositionRepository = $vehiclePositionRepository;
        $this->stopCacheService = $stopCacheService;
        $this->linkGenerator = $linkGenerator;
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
                $vehicle->getMapLabel(),
                $this->getVehicleInfoWindowLines($vehicle)
            );
        }
        return $objects;
    }

    /**
     * @return string[]
     */
    private function getVehicleInfoWindowLines(IVehicle $vehicle): array
    {
        $lines = [
            sprintf(
                '<i class="%s"></i> %s %s',
                VehicleType::getIcon($vehicle->getVehicleType()),
                $vehicle->getCompany(),
                $vehicle->getRegistrationNumber()
            ),
            sprintf(
                '%s %s',
                $vehicle->getRouteId(),
                $vehicle->getFinalStation()
            ),
            sprintf(
                'Zpoždění %s sekund',
                $vehicle->getDelayInSeconds()
            ),
        ];

        if ($vehicle->getLastStopId() !== null) {
            $lines[] = sprintf(
                'Poslední známá zastávka: %s',
                $this->stopCacheService->getStop($vehicle->getLastStopId())
            );
        }

        if ($vehicle->getNextStopId() !== null) {
            $lines[] = sprintf(
                'Příští zastávka zastávka: %s',
                $this->stopCacheService->getStop($vehicle->getNextStopId())
            );
        }

        $statisticButton = Html::el('a');
        $statisticButton->class = 'btn btn-primary btn-sm';
        $statisticButton->href = $this->linkGenerator->link(
            'Front:Statistic:trip',
            ['tripId' => $vehicle->getTripId()]
        );
        $statisticButton->setText('Podrobné statistiky');

        $lines[] = $statisticButton->toHtml();

        return $lines;
    }
}
