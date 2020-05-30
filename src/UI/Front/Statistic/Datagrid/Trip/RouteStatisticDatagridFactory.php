<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Datagrid\Trip;

use App\Transport\Prague\Statistic\TripList\TripListRepository;
use App\UI\Front\Base\FrontDatagrid;
use App\UI\Front\Base\FrontDatagridFactory;

class RouteStatisticDatagridFactory
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
		$grid = $this->frontDatagridFactory->create();

		$grid->setDataSource($this->tripListRepository->createQueryBuilder());
		$grid->addColumnText('routeId', 'Route ID')->setFilterText();
		$grid->addColumnText('tripId', 'Trip ID')->setFilterText();
		$grid->addColumnText('lastFinalStation', 'Poslední cílová stanice')->setFilterText();
		$grid->addColumnDateTime('newestKnownPosition', 'Poslední známá poloha')->setSortable();

		$grid->addAction('detail', 'Detail', 'Statistic:trip', ['tripId' => 'tripId'])
			->setIcon('arrow-right')
			->setClass('btn btn-sm btn-primary');

		return $grid;
	}
}
