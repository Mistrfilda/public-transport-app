<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Control;

use App\Transport\Prague\Stop\StopRepository;
use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\UI\Shared\Statistic\Statistic;
use App\Utils\DatetimeFactory;
use Nette\Application\UI\Control;

class StatisticControl extends Control
{
    /** @var VehiclePositionRepository */
    private $vehiclePositionRepository;

    /** @var StopRepository */
    private $stopRepository;

    public function __construct(
        VehiclePositionRepository $VehiclePositionRepository,
        StopRepository $stopRepository
    ) {
        $this->vehiclePositionRepository = $VehiclePositionRepository;
        $this->stopRepository = $stopRepository;
    }

    public function render(): void
    {
        $this->getTemplate()->statistics = $this->buildStatistics();
        $this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
        $this->getTemplate()->render();
    }

    /**
     * @return Statistic[]
     */
    private function buildStatistics(): array
    {
        $statistics = [];
        $lastVehiclePosition = $this->vehiclePositionRepository->findLast();

        if ($lastVehiclePosition !== null) {
            $statistics[] = new Statistic(
                'success',
                'Poslední známa poloha vozidel',
                $lastVehiclePosition->getCreatedAt()->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
                'fas fa-clock fa-2x text-gray-300',
                'border-left-',
                'col-xl-12 col-md-12'
            );

            $statistics[] = new Statistic(
                'success',
                'Počet poloh vozidel',
                (string) $lastVehiclePosition->getVehiclesCount(),
                'fas fa-bus fa-2x text-gray-300'
            );
        }

        $statistics[] = new Statistic(
            'primary',
            'Celkový počet zastávek',
            (string) $this->stopRepository->getStopsCount(),
            'fas fa-ruler-vertical fa-2x text-gray-300'
        );

        return $statistics;
    }
}
