<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueParkingLot\Control;

use App\Transport\Prague\Parking\ParkingLot;
use App\Transport\Prague\Parking\ParkingLotOccupancyRepository;
use App\Transport\Prague\Parking\ParkingLotRepository;
use App\UI\Front\Base\BaseControl;
use App\UI\Front\Control\Datagrid\Datasource\DoctrineDataSource;
use App\UI\Front\Control\Datagrid\FrontDatagrid;
use App\UI\Front\Control\Datagrid\FrontDatagridFactory;
use App\UI\Front\TailwindConstant;

class ParkingLotCardControl extends BaseControl
{
	private ParkingLotRepository $parkingLotRepository;

	private ParkingLotOccupancyRepository $parkingLotOccupancyRepository;

	private FrontDatagridFactory $frontDatagridFactory;

	public function __construct(
		ParkingLotRepository $parkingLotRepository,
		ParkingLotOccupancyRepository $parkingLotOccupancyRepository,
		FrontDatagridFactory $frontDatagridFactory
	) {
		$this->parkingLotRepository = $parkingLotRepository;
		$this->parkingLotOccupancyRepository = $parkingLotOccupancyRepository;
		$this->frontDatagridFactory = $frontDatagridFactory;
	}

	public function render(): void
	{
		$this->getTemplate()->lastUpdateTime = $this->parkingLotOccupancyRepository->getLastParkingDate();
		$this->getTemplate()->parkingLots = $this->parkingLotRepository->findAll();
		$this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
		$this->getTemplate()->render();
	}

	protected function createComponentParkingLotTable(): FrontDatagrid
	{
		$source = new DoctrineDataSource($this->parkingLotRepository->createQueryBuilderForDatagrid());
		$grid = $this->frontDatagridFactory->create($source);

		$grid->addColumnText('Jméno', 'name');
		$grid->addColumnText('Adresa', 'address');

		$grid->addColumnBadge(
			'Celkový počet míst',
			'totalSpaces',
			TailwindConstant::BLUE,
			function (ParkingLot $parkingLot) {
				return (string) $parkingLot->getLastParkingLotOccupancy()->getTotalSpaces();
			}
		);

		$grid->addColumnBadge(
			'Počet volných míst',
			'freeSpaces',
			TailwindConstant::GREEN,
			function (ParkingLot $parkingLot) {
				return (string) $parkingLot->getLastParkingLotOccupancy()->getFreeSpaces();
			}
		);

		$grid->addColumnBadge(
			'Počet obsazených míst',
			'occupiedSpaces',
			TailwindConstant::RED,
			function (ParkingLot $parkingLot) {
				return (string) $parkingLot->getLastParkingLotOccupancy()->getOccupiedSpaces();
			}
		);

		$grid->addColumnBadge(
			'Obsazenost',
			'occupancy',
			TailwindConstant::RED,
			function (ParkingLot $parkingLot): string {
				return number_format(100 - $parkingLot->calculateOccupancyPercentage()) . ' %';
			},
			function (ParkingLot $parkingLot): string {
				$currentOccupancy = $parkingLot->calculateOccupancyPercentage();
				if ($currentOccupancy < 80) {
					return TailwindConstant::RED;
				}

				if ($currentOccupancy < 50) {
					return TailwindConstant::YELLOW;
				}

				return TailwindConstant::GREEN;
			}
		);

		return $grid;
	}
}
