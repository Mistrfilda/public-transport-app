<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\Trip;

use App\Transport\Prague\Statistic\ChartDataProvider\TripStatisticDataCountChartDataProvider;
use App\Transport\Prague\Statistic\ChartDataProvider\TripStatisticDelayChartDataProvider;
use App\Transport\Prague\Statistic\ChartDataProvider\TripStatisticVehicleRegistrationChartDataProvider;
use App\Transport\Prague\Statistic\TripStatisticData;
use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Admin\Control\Statistic\Chart\ChartControl;
use App\UI\Admin\Control\Statistic\Chart\ChartControlFactory;
use App\UI\Front\Base\BaseControl;
use App\UI\Front\Control\Datagrid\FrontDatagrid;
use App\UI\Front\Statistic\Datagrid\Trip\TripStatisticDataDatagridFactory;
use App\UI\Front\Statistic\FrontStatistic;
use App\UI\Shared\Statistic\Chart\ChartType;
use Doctrine\ORM\NoResultException;
use Mistrfilda\Datetime\DatetimeFactory;

class TripStatisticControl extends BaseControl
{
	private string $tripId;

	private TripStatisticDataRepository $tripStatisticDataRepository;

	private ChartControlFactory $chartControlFactory;

	private TripStatisticDelayChartDataProvider $tripStatisticChartDataProvider;

	private TripStatisticDataCountChartDataProvider $tripStatisticDataCountChartDataProvider;

	private TripStatisticVehicleRegistrationChartDataProvider $tripStatisticVehicleRegistrationChartDataProvider;

	private TripStatisticDataDatagridFactory $tripStatisticDataDatagridFactory;

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
		$template = $this->createTemplate(TripStatisticControlTemplate::class);
		try {
			$tripStatistic = $this->tripStatisticDataRepository->findByTripIdSingle($this->tripId);
		} catch (NoResultException $e) {
			$template->setFile(__DIR__ . '/TripStatisticNotFound.latte');
			$template->render();
			return;
		}

		$tripStatisticCount = $this->tripStatisticDataRepository->getCountTripsByTripId($this->tripId);

		$template->statistics = $this->buildStatisticBoxes($tripStatistic, $tripStatisticCount);
		$template->tripId = $this->tripId;
		$template->setFile(str_replace('.php', '.latte', __FILE__));
		$template->render();
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
	 * @return FrontStatistic[]
	 */
	private function buildStatisticBoxes(TripStatisticData $tripStatisticData, int $tripStatisticDataCount): array
	{
		$statistics = [];

		$statistics[] = new FrontStatistic(
			'Linka',
			$tripStatisticData->getRouteId(),
			'fas fa-bus fa-2x',
			FrontStatistic::BLUE
		);

		$statistics[] = new FrontStatistic(
			'Trip ID',
			$tripStatisticData->getTripId(),
			'fas fa-list-alt fa-2x',
			FrontStatistic::INDIGO
		);

		$statistics[] = new FrontStatistic(
			'Poslední data dostupná ze dne',
			$tripStatisticData->getDate()->format(DatetimeFactory::DEFAULT_DATE_FORMAT),
			'fas fa-history fa-2x',
			FrontStatistic::RED
		);

		$statistics[] = new FrontStatistic(
			'Poslední cílová stanice',
			$tripStatisticData->getFinalStation(),
			'fas fa-flag fa-2x',
			FrontStatistic::TEAL
		);

		$statistics[] = new FrontStatistic(
			'Celkový počet uložených dat',
			(string) $tripStatisticDataCount,
			'fas fa-database fa-2x',
			FrontStatistic::LIGHT_BLUE
		);

		$averageDelay = (int) $this->tripStatisticDataRepository->getAvgTripDelay($this->tripId);
		if ($averageDelay <= 0) {
			$averageDelay = 0;
		}

		$statistics[] = new FrontStatistic(
			'Průměrné zpoždění',
			(string) $averageDelay . ' sekund',
			'fas fa-database fa-2x',
			FrontStatistic::GRAY
		);

		return $statistics;
	}
}
