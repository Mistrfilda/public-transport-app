<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueStatistic\Datagrid;

use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;

class StatisticDatagridFactory
{
	private AdminDatagridFactory $adminDatagridFactory;

	private TripStatisticDataRepository $tripStatisticDataRepository;

	public function __construct(
		AdminDatagridFactory $adminDatagridFactory,
		TripStatisticDataRepository $tripStatisticDataRepository
	) {
		$this->adminDatagridFactory = $adminDatagridFactory;
		$this->tripStatisticDataRepository = $tripStatisticDataRepository;
	}

	public function create(): AdminDatagrid
	{
		$grid = $this->adminDatagridFactory->create();
		$grid->setDataSource($this->tripStatisticDataRepository->createQueryBuilder());

		$grid->addColumnText('tripId', 'Trip ID')->setFilterText();
		$grid->addColumnText('routeId', 'Route ID')->setFilterText();
		$grid->addColumnText('finalStation', 'Final station')->setFilterText();
		$grid->addColumnText('wheelchairAccessible', 'Wheelchair accessible');
		$grid->addColumnDate('date', 'Date')->setFilterDate();
		$grid->addColumnDateTime('oldestKnownPosition', 'Oldest known position');
		$grid->addColumnDateTime('newestKnownPosition', 'Newest known position');
		$grid->addColumnText('highestDelay', 'Highest delay')->setSortable();
		$grid->addColumnText('averageDelay', 'Average delay');
		$grid->addColumnText('company', 'Company')->setFilterText();
		$grid->addColumnText('vehicleId', 'Vehicle ID')->setFilterText();
		$grid->addColumnText('vehicleType', 'Vehicle Type')->setFilterText();
		$grid->addColumnText('positionsCount', 'Positions count');

		return $grid;
	}
}
