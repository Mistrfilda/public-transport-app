<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\ChartDataProvider;

use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Shared\Statistic\Chart\ChartData;
use App\UI\Shared\Statistic\Chart\ChartException;
use App\UI\Shared\Statistic\Chart\IChartDataProvider;

class TripStatisticVehicleRegistrationChartDataProvider implements IChartDataProvider, ITripStatisticChartDataProvider
{
	private TripStatisticDataRepository $tripStatisticDataRepository;

	private ?string $tripId = null;

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

		$chartData = new ChartData('Vozidla na lince', true);

		foreach ($this->tripStatisticDataRepository->getVehicleTypeCountByTripId($this->tripId) as $vehicleCount) {
			$vehicleId = 'Neznámé';
			if ($vehicleCount['vehicleId'] !== null) {
				$vehicleId = $vehicleCount['vehicleId'];
			}

			$chartData->add(
				$vehicleId,
				(int) $vehicleCount['count']
			);
		}

		return $chartData;
	}
}
