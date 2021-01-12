<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Datagrid\Trip;

use App\Transport\Prague\Statistic\TripStatisticData;
use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Front\Control\Datagrid\Datasource\DoctrineDataSource;
use App\UI\Front\Control\Datagrid\FrontDatagrid;
use App\UI\Front\Control\Datagrid\FrontDatagridFactory;
use App\UI\Front\TailwindConstant;

class TripStatisticDataDatagridFactory
{
	private FrontDatagridFactory $frontDatagridFactory;

	private TripStatisticDataRepository $tripStatisticDataRepository;

	public function __construct(
		FrontDatagridFactory $frontDatagridFactory,
		TripStatisticDataRepository $tripStatisticDataRepository
	) {
		$this->frontDatagridFactory = $frontDatagridFactory;
		$this->tripStatisticDataRepository = $tripStatisticDataRepository;
	}

	public function create(string $tripId): FrontDatagrid
	{
		$qb = $this->tripStatisticDataRepository->createQueryBuilder();
		$qb->andWhere($qb->expr()->eq('tripStatistic.tripId', ':tripId'));
		$qb->setParameter('tripId', $tripId);

		$datasource = new DoctrineDataSource($qb);
		$grid = $this->frontDatagridFactory->create($datasource);

		$grid->addColumnDate('date', 'Datum');
		$grid->addColumnBadge('dayName', 'Nazev dne', TailwindConstant::BLUE);

		$grid->addColumnBadge(
			'isCzechHoliday',
			'Státní svátek',
			TailwindConstant::RED,
			function (TripStatisticData $tripStatisticData) {
				if ($tripStatisticData->isCzechHoliday()) {
					return 'Ano';
				}

				return 'Ne';
			},
			function (TripStatisticData $tripStatisticData) {
				if ($tripStatisticData->isCzechHoliday()) {
					return TailwindConstant::GREEN;
				}

				return TailwindConstant::RED;
			}
		);

//		$grid->setFilterSelect($czechHoliday, [
//			0 => 'Ne',
//			1 => 'Ano',
//		]);

//		$grid->addColumnDateTime('oldestKnownPosition', 'První známá poloha');
//		$grid->addColumnDateTime('newestKnownPosition', 'Poslední známá poloha');

//		$grid->addColumnText('routeId', 'Route ID')->setFilterText();
		$grid->addColumnText('company', 'Společnost')->setFilterText();

		$grid->addColumnText(
			'vehicleId',
			'Vozidlo',
			function (TripStatisticData $tripStatisticData): string {
				if ($tripStatisticData->getVehicleId() !== null) {
					return $tripStatisticData->getVehicleId();
				}

				return FrontDatagrid::NULLABLE_PLACEHOLDER;
			}
		)->setFilterText();

		$grid->addColumnText('finalStation', 'Konečná stanice')->setFilterText();

		$grid->addColumnBadge(
			'averageDelay',
			'Průměrné zpoždění',
			TailwindConstant::GREEN,
			function (TripStatisticData $tripStatisticData): string {
				return sprintf('%s sekund', $tripStatisticData->getAverageDelay());
			},
			function (TripStatisticData $tripStatisticData): string {
				if ($tripStatisticData->getAverageDelay() > 120) {
					return TailwindConstant::RED;
				}

				if ($tripStatisticData->getAverageDelay() > -120) {
					return TailwindConstant::GREEN;
				}

				return TailwindConstant::INDIGO;
			}
		);
		$grid->addColumnBadge(
			'highestDelay',
			'Nejvyšší zpoždění',
			TailwindConstant::GREEN,
			function (TripStatisticData $tripStatisticData): string {
				return sprintf('%s sekund', $tripStatisticData->getHighestDelay());
			},
			function (TripStatisticData $tripStatisticData): string {
				if ($tripStatisticData->getHighestDelay() > 120) {
					return TailwindConstant::RED;
				}

				if ($tripStatisticData->getHighestDelay() > -120) {
					return TailwindConstant::GREEN;
				}

				return TailwindConstant::INDIGO;
			}
		);

		$grid->addColumnBadge(
			'lastPositionDelay',
			'Zpoždění v cílové zastávce',
			TailwindConstant::GREEN,
			function (TripStatisticData $tripStatisticData): string {
				if ($tripStatisticData->getLastPositionDelay() === null) {
					return FrontDatagrid::NULLABLE_PLACEHOLDER;
				}

				return sprintf('%s sekund', $tripStatisticData->getLastPositionDelay());
			},
			function (TripStatisticData $tripStatisticData): string {
				if ($tripStatisticData->getLastPositionDelay() > 120) {
					return TailwindConstant::RED;
				}

				if ($tripStatisticData->getLastPositionDelay() > -120) {
					return TailwindConstant::GREEN;
				}

				return TailwindConstant::YELLOW;
			}
		);

		//$grid->setDefaultSort(['date' => 'desc']);

		return $grid;
	}
}
