<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Datagrid\Trip;

use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Front\Base\FrontDatagrid;
use App\UI\Front\Base\FrontDatagridFactory;

class RouteStatisticDatagridFactory
{
	/** @var FrontDatagridFactory */
	private $frontDatagridFactory;

	/** @var TripStatisticDataRepository */
	private $tripStatisticDataRepository;

	public function __construct(
		FrontDatagridFactory $frontDatagridFactory,
		TripStatisticDataRepository $tripStatisticDataRepository
	) {
		$this->frontDatagridFactory = $frontDatagridFactory;
		$this->tripStatisticDataRepository = $tripStatisticDataRepository;
	}

	public function create(): FrontDatagrid
	{
		$grid = $this->frontDatagridFactory->create();

		$qb = $this->tripStatisticDataRepository->createQueryBuilder();
		$qb->select(
			'tripStatistic.tripId, tripStatistic.routeId, tripStatistic.finalStation, max(tripStatistic.newestKnownPosition) as newestKnownPosition'
		);
		$qb->groupBy('tripStatistic.tripId, tripStatistic.routeId, tripStatistic.finalStation');

		$grid->setPrimaryKey('tripId');
		$grid->setDataSource($qb->getQuery()->getResult());
		$grid->addColumnText('routeId', 'Route ID')->setFilterText();
		$grid->addColumnText('tripId', 'Trip ID')->setFilterText();
		$grid->addColumnText('finalStation', 'Poslední cílová stanice')->setFilterText();
		$grid->addColumnDateTime('newestKnownPosition', 'Poslední známá poloha')->setSortable();

		$grid->addAction('detail', 'Detail', 'Statistic:trip', ['tripId' => 'tripId'])
			->setIcon('arrow-right')
			->setClass('btn btn-sm btn-primary');

		return $grid;
	}
}
