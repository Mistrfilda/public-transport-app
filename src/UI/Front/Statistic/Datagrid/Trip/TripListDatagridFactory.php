<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Datagrid\Trip;

use App\Transport\Prague\Statistic\TripList\TripListRepository;
use App\UI\Front\Control\Datagrid\Action\DatagridActionParameter;
use App\UI\Front\Control\Datagrid\Datasource\DoctrineDataSource;
use App\UI\Front\Control\Datagrid\FrontDatagrid;
use App\UI\Front\Control\Datagrid\FrontDatagridFactory;
use App\UI\Front\TailwindConstant;

class TripListDatagridFactory
{
	private FrontDatagridFactory $frontDatagridFactory;

	private TripListRepository $tripListRepository;

	public function __construct(
		FrontDatagridFactory $frontDatagridFactory,
		TripListRepository $tripListRepository
	) {
		$this->frontDatagridFactory = $frontDatagridFactory;
		$this->tripListRepository = $tripListRepository;
	}

	public function create(): FrontDatagrid
	{
		$datasource = new DoctrineDataSource($this->tripListRepository->createQueryBuilder());
		$grid = $this->frontDatagridFactory->create($datasource);

		$grid->addColumnText('routeId', 'Route ID')->setFilterText();
		$grid->addColumnText('tripId', 'Trip ID')->setFilterText();
		$grid->addColumnText('lastFinalStation', 'Poslední cílová stanice')->setFilterText();

		$grid->addColumnDatetime('newestKnownPosition', 'Poslední známá poloha');

		$grid->addColumnBadge('countOfStatistics', 'Počet statistik', TailwindConstant::BLUE)->setFilterText();

		$grid->addAction(
			'detail',
			'Detail',
			'StatisticDetail:default',
			[
				new DatagridActionParameter('tripId', 'tripId'),
			]
		);

		return $grid;
	}
}
