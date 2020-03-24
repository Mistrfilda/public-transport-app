<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\ChartDataProvider;

use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Shared\Statistic\Chart\ChartException;
use App\UI\Shared\Statistic\Chart\LineChart\ChartData;
use App\UI\Shared\Statistic\Chart\LineChart\IChartDataProvider;
use App\Utils\DatetimeFactory;

class TripStatisticDataCountChartDataProvider implements IChartDataProvider, ITripStatisticChartDataProvider
{
    /** @var TripStatisticDataRepository */
    private $tripStatisticDataRepository;

    /** @var string|null */
    private $tripId = null;

    public function __construct(TripStatisticDataRepository $tripStatisticDataRepository)
    {
        $this->tripStatisticDataRepository = $tripStatisticDataRepository;
    }

    public function prepare(string $tripId): void
    {
        $this->tripId = $tripId;
    }

    public function getChartData(): ChartData
    {
        if ($this->tripId === null) {
            throw new ChartException('Please call ::prepare before calling getChartData');
        }

        $tripData = $this->tripStatisticDataRepository->findByTripId($this->tripId, 30);

        $chartData = new ChartData('Počet poloh vozidel během 30 dní', true);

        foreach (array_reverse($tripData) as $trip) {
            $chartData->add(
                $trip->getDate()->format(DatetimeFactory::DEFAULT_DATE_FORMAT),
                $trip->getPositionsCount()
            );
        }

        return $chartData;
    }
}
