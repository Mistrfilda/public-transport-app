<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\Trip;

use App\Transport\Prague\Statistic\ChartDataProvider\TripStatisticChartDataProvider;
use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Front\Base\BaseControl;
use App\UI\Shared\Statistic\Chart\ChartType;
use App\UI\Shared\Statistic\Chart\Control\ChartControl;
use App\UI\Shared\Statistic\Chart\Control\ChartControlFactory;

class TripStatisticControl extends BaseControl
{
    /** @var string */
    private $tripId;

    /** @var TripStatisticDataRepository */
    private $tripStatisticDataRepository;

    /** @var ChartControlFactory */
    private $chartControlFactory;

    /** @var TripStatisticChartDataProvider */
    private $tripStatisticChartDataProvider;

    public function __construct(
        string $tripId,
        TripStatisticDataRepository $tripStatisticDataRepository,
        ChartControlFactory $chartControlFactory,
        TripStatisticChartDataProvider $tripStatisticChartDataProvider
    ) {
        $this->tripStatisticDataRepository = $tripStatisticDataRepository;
        $this->tripId = $tripId;
        $this->chartControlFactory = $chartControlFactory;
        $this->tripStatisticChartDataProvider = $tripStatisticChartDataProvider;
    }

    public function render(): void
    {
        $this->getTemplate()->tripStatistics = $this->tripStatisticDataRepository->findByTripId($this->tripId, null);
        $this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
        $this->getTemplate()->render();
    }

    protected function createComponentTestGraph(): ChartControl
    {
        return $this->chartControlFactory->create(ChartType::BAR, $this->tripStatisticChartDataProvider);
    }

    protected function createComponentTestGraph2(): ChartControl
    {
        return $this->chartControlFactory->create(ChartType::LINE, $this->tripStatisticChartDataProvider);
    }

    protected function createComponentTestGraph3(): ChartControl
    {
        return $this->chartControlFactory->create(ChartType::DOUGHNUT, $this->tripStatisticChartDataProvider);
    }
}
