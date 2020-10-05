<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueParkingLot\Datagrid;

use App\Transport\Prague\Parking\ParkingLotOccupancyRepository;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;
use Ramsey\Uuid\UuidInterface;

class ParkingLotOccupancyDatagridFactory
{
	private ParkingLotOccupancyRepository $parkingLotOccupancyRepository;

	private AdminDatagridFactory $adminDatagridFactory;

	public function __construct(
		ParkingLotOccupancyRepository $parkingLotOccupancyRepository,
		AdminDatagridFactory $adminDatagridFactory
	) {
		$this->parkingLotOccupancyRepository = $parkingLotOccupancyRepository;
		$this->adminDatagridFactory = $adminDatagridFactory;
	}

	public function create(UuidInterface $parkingLotId): AdminDatagrid
	{
		$grid = $this->adminDatagridFactory->create();

		$grid->setDataSource($this->parkingLotOccupancyRepository->createQueryBuilderForDatagrid($parkingLotId));

		$grid->addColumnDateTime('createdAt', 'Parking spaces download at')->setSortable();
		$grid->addColumnText('totalSpaces', 'Total parking spaces')->setSortable();
		$grid->addColumnText('freeSpaces', 'Free parking spaces')->setSortable();
		$grid->addColumnText('occupiedSpaces', 'Occupied parking spaces')->setSortable();

		return $grid;
	}
}
