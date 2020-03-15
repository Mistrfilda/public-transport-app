<?php

declare(strict_types=1);

namespace App\UI\Front\Homepage;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\Vehicle\VehicleMapObjectProvider;
use App\UI\Front\FrontPresenter;
use App\UI\Shared\Map\MapControl;
use App\UI\Shared\Map\MapControlFactory;
use App\UI\Shared\Statistic\Control\StatisticControl;
use App\UI\Shared\Statistic\Control\StatisticControlFactory;

class HomepagePresenter extends FrontPresenter
{
    /** @var DepartureTableRepository */
    private $departureTableRepository;

    /** @var StatisticControlFactory */
    private $statisticControlFactory;

    /** @var MapControlFactory */
    private $mapControlFactory;

    /** @var VehicleMapObjectProvider */
    private $vehicleMapObjectProvider;

    public function __construct(
        DepartureTableRepository $departureTableRepository,
        StatisticControlFactory $statisticControlFactory,
        MapControlFactory $mapControlFactory,
        VehicleMapObjectProvider $vehicleMapObjectProvider
    ) {
        parent::__construct();
        $this->departureTableRepository = $departureTableRepository;
        $this->statisticControlFactory = $statisticControlFactory;
        $this->mapControlFactory = $mapControlFactory;
        $this->vehicleMapObjectProvider = $vehicleMapObjectProvider;
    }

    public function renderDefault(): void
    {
        $this->template->departureTables = $this->departureTableRepository->findAll();
    }

    protected function createComponentStatisticControl(): StatisticControl
    {
        return $this->statisticControlFactory->create();
    }

    protected function createComponentMapControl(): MapControl
    {
        return $this->mapControlFactory->create($this->vehicleMapObjectProvider->getMapObjects());
    }
}
