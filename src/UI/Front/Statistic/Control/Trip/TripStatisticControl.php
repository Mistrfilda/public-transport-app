<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\Trip;

use App\Transport\Prague\Statistic\ChartDataProvider\TripStatisticDataCountChartDataProvider;
use App\Transport\Prague\Statistic\ChartDataProvider\TripStatisticDelayChartDataProvider;
use App\Transport\Prague\Statistic\ChartDataProvider\TripStatisticVehicleRegistrationChartDataProvider;
use App\Transport\Prague\Statistic\TripStatisticData;
use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Front\Base\BaseControl;
use App\UI\Front\Base\FrontDatagrid;
use App\UI\Front\Statistic\Datagrid\Trip\TripStatisticDataDatagridFactory;
use App\UI\Shared\Statistic\Chart\ChartType;
use App\UI\Shared\Statistic\Chart\Control\ChartControl;
use App\UI\Shared\Statistic\Chart\Control\ChartControlFactory;
use App\UI\Shared\Statistic\Statistic;
use App\Utils\DatetimeFactory;
use Doctrine\ORM\NoResultException;

class TripStatisticControl extends BaseControl
{
    /** @var string */
    private $tripId;

    /** @var TripStatisticDataRepository */
    private $tripStatisticDataRepository;

    /** @var ChartControlFactory */
    private $chartControlFactory;

    /** @var TripStatisticDelayChartDataProvider */
    private $tripStatisticChartDataProvider;

    /** @var TripStatisticDataCountChartDataProvider */
    private $tripStatisticDataCountChartDataProvider;

    /** @var TripStatisticVehicleRegistrationChartDataProvider */
    private $tripStatisticVehicleRegistrationChartDataProvider;

    /** @var TripStatisticDataDatagridFactory */
    private $tripStatisticDataDatagridFactory;

    public function __construct(
        string $tripId,
        TripStatisticDataRepository $tripStatisticDataRepository,
        ChartControlFactory $chartControlFactory,
        TripStatisticDelayChartDataProvider $tripStatisticChartDataProvider,
        TripStatisticDataCountChartDataProvider $tripStatisticDataCountChartDataProvider,
        TripStatisticVehicleRegistrationChartDataProvider $tripStatisticVehicleRegistrationChartDataProvider,
        TripStatisticDataDatagridFactory $tripStatisticDataDatagridFactory
    ) {
        $this->tripStatisticDataRepository = $tripStatisticDataRepository;
        $this->tripId = $tripId;
        $this->chartControlFactory = $chartControlFactory;
        $this->tripStatisticChartDataProvider = $tripStatisticChartDataProvider;
        $this->tripStatisticDataCountChartDataProvider = $tripStatisticDataCountChartDataProvider;
        $this->tripStatisticVehicleRegistrationChartDataProvider = $tripStatisticVehicleRegistrationChartDataProvider;
        $this->tripStatisticDataDatagridFactory = $tripStatisticDataDatagridFactory;

        $this->prepareChartDataProviders();
    }

    public function render(): void
    {
        try {
            $tripStatistic = $this->tripStatisticDataRepository->findByTripIdSingle($this->tripId);
        } catch (NoResultException $e) {
            $this->getTemplate()->setFile(__DIR__ . '/TripStatisticNotFound.latte');
            $this->getTemplate()->render();
            return;
        }

        $tripStatisticCount = $this->tripStatisticDataRepository->getCountTripsByTripId($this->tripId);

        $this->getTemplate()->statisticBoxes = $this->buildStatisticBoxes($tripStatistic, $tripStatisticCount);
        $this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
        $this->getTemplate()->render();
    }

    protected function createComponentTripDataCountChart(): ChartControl
    {
        return $this->chartControlFactory->create(
            ChartType::BAR,
            'Počet poloh vozidel během 30 dnů',
            $this->tripStatisticDataCountChartDataProvider
        );
    }

    protected function createComponentTripVehicleTypeChart(): ChartControl
    {
        return $this->chartControlFactory->create(
            ChartType::DOUGHNUT,
            'Vozidla na lince',
            $this->tripStatisticVehicleRegistrationChartDataProvider
        );
    }

    protected function createComponentLineDelayChart(): ChartControl
    {
        return $this->chartControlFactory->create(
            ChartType::LINE,
            'Průměrné zpoždění během 30 dnů',
            $this->tripStatisticChartDataProvider,
        );
    }

    protected function createComponentTripStatisticDataDatagrid(): FrontDatagrid
    {
        return $this->tripStatisticDataDatagridFactory->create($this->tripId);
    }

    private function prepareChartDataProviders(): void
    {
        $this->tripStatisticChartDataProvider->prepare($this->tripId);
        $this->tripStatisticDataCountChartDataProvider->prepare($this->tripId);
        $this->tripStatisticVehicleRegistrationChartDataProvider->prepare($this->tripId);
    }

    /**
     * @return Statistic[]
     */
    private function buildStatisticBoxes(TripStatisticData $tripStatisticData, int $tripStatisticDataCount): array
    {
        $statistics = [];

        $statistics[] = new Statistic(
            Statistic::CONTEXTUAL_PRIMARY,
            'Linka',
            $tripStatisticData->getRouteId(),
            'fas fa-bus fa-2x',
            'border-left-',
            'col-xl-3 col-md-6 mb-4'
        );

        $statistics[] = new Statistic(
            Statistic::CONTEXTUAL_WARNING,
            'Trip ID',
            $tripStatisticData->getTripId(),
            'fas fa-list-alt fa-2x',
            'border-left-',
            'col-xl-3 col-md-6 mb-4'
        );

        $statistics[] = new Statistic(
            Statistic::CONTEXTUAL_DANGER,
            'Poslední data dostupná ze dne',
            $tripStatisticData->getDate()->format(DatetimeFactory::DEFAULT_DATE_FORMAT),
            'fas fa-history fa-2x',
            'border-left-',
            'col-xl-3 col-md-6 mb-4'
        );

        $statistics[] = new Statistic(
            Statistic::CONTEXTUAL_SUCCESS,
            'Poslední cílová stanice',
            $tripStatisticData->getFinalStation(),
            'fas fa-ruler-vertical fa-2x',
            'border-left-',
            'col-xl-3 col-md-6 mb-4'
        );

        $statistics[] = new Statistic(
            Statistic::CONTEXTUAL_INFO,
            'Celkový počet uložených dat',
            (string) $tripStatisticDataCount,
            'fas fa-database fa-2x',
            'border-left-',
            'col-xl-3 col-md-6 mb-4'
        );

        $averageDelay = (int) $this->tripStatisticDataRepository->getAvgTripDelay($this->tripId);
        if ($averageDelay <= 0) {
            $averageDelay = 0;
        }

        $statistics[] = new Statistic(
            Statistic::CONTEXTUAL_SECONDARY,
            'Průměrné zpoždění',
            (string) $averageDelay . ' sekund',
            'fas fa-database fa-2x',
            'border-left-',
            'col-xl-3 col-md-6 mb-4'
        );

        return $statistics;
    }
}
