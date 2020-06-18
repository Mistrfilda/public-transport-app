<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Datagrid\Trip;

use App\Transport\Prague\Statistic\TripStatisticData;
use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Front\Base\FrontDatagrid;
use App\UI\Front\Base\FrontDatagridFactory;

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
		$grid = $this->frontDatagridFactory->create();

		$qb = $this->tripStatisticDataRepository->createQueryBuilder();
		$qb->andWhere($qb->expr()->eq('tripStatistic.tripId', ':tripId'));
		$qb->setParameter('tripId', $tripId);
		$grid->setDataSource($qb);

		$grid->addColumnDate('date', 'Datum')->setSortable()->setFilterDate();
		$grid->addColumnText('routeId', 'Route ID')->setFilterText();
		$grid->addColumnText('company', 'Společnost')->setFilterText();
		$grid->addColumnText('vehicleId', 'Vozidlo')
			->setRenderer(function (TripStatisticData $tripStatisticData): string {
				if ($tripStatisticData->getVehicleId() !== null) {
					return $tripStatisticData->getVehicleId();
				}

				return FrontDatagrid::NULLABLE_PLACEHOLDER;
			})->setFilterText();
		$grid->addColumnText('finalStation', 'Konečná stanice')->setFilterText();

		$grid->addColumnText('averageDelay', 'Průměrné zpoždění')->setSortable();
		$grid->addColumnText('highestDelay', 'Nejvyšší zpoždění')->setSortable();

		$grid->setDefaultSort(['date' => 'desc']);

		return $grid;
	}
}
