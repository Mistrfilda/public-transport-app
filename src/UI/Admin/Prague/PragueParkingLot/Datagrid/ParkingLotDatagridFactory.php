<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueParkingLot\Datagrid;

use App\Transport\Prague\Parking\ParkingLot;
use App\Transport\Prague\Parking\ParkingLotRepository;
use App\Transport\Prague\Parking\ParkingType;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;

class ParkingLotDatagridFactory
{
	private ParkingLotRepository $parkingLotRepository;

	private AdminDatagridFactory $adminDatagridFactory;

	public function __construct(
		ParkingLotRepository $parkingLotRepository,
		AdminDatagridFactory $adminDatagridFactory
	) {
		$this->parkingLotRepository = $parkingLotRepository;
		$this->adminDatagridFactory = $adminDatagridFactory;
	}

	public function create(): AdminDatagrid
	{
		$grid = $this->adminDatagridFactory->create();

		$grid->setDataSource($this->parkingLotRepository->createQueryBuilderForDatagrid());
		$grid->addColumnText('id', 'ID')->setFilterText();
		$grid->addColumnText('name', 'Name')->setSortable()->setFilterText();
		$grid->addColumnText('address', 'Address')->setFilterText();

		$grid->addColumnText('type', 'Type')
			->setRenderer(function (ParkingLot $parkingLot) {
				if (ParkingType::exists($parkingLot->getParkingType())) {
					return ParkingType::LABELS[$parkingLot->getParkingType()];
				}

				return 'N/A';
			});

		$grid->addColumnDateTime(
			'occupancyDate',
			'Parking spaces download at',
			'lastParkingLotOccupancy.createdAt'
		)->setSortable();

		$grid->addColumnText('totalSpaces', 'Total parking spaces', 'lastParkingLotOccupancy.totalSpaces')->setSortable();

		$grid->addColumnText('freeSpaces', 'Free parking spaces', 'lastParkingLotOccupancy.freeSpaces')->setSortable();

		$grid->addColumnText(
			'occupiedSpaces',
			'Occupied parking spaces',
			'lastParkingLotOccupancy.occupiedSpaces'
		)->setSortable();

		$grid->addAction(
			'parkingLotOccupancy',
			'All parking lots occupancies',
			'PragueParkingLotOccupancy:parkingLotOccupancy'
		)
			->setIcon('parking')
			->setClass('btn btn-sm btn-info');

		return $grid;
	}
}
