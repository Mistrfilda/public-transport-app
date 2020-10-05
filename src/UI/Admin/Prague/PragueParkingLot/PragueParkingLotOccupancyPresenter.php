<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueParkingLot;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Prague\PragueParkingLot\Datagrid\ParkingLotOccupancyDatagridFactory;
use Ramsey\Uuid\Rfc4122\UuidV4;

class PragueParkingLotOccupancyPresenter extends AdminPresenter
{
	private ParkingLotOccupancyDatagridFactory $parkingLotOccupancyDatagridFactory;

	public function __construct(ParkingLotOccupancyDatagridFactory $parkingLotOccupancyDatagridFactory)
	{
		parent::__construct();
		$this->parkingLotOccupancyDatagridFactory = $parkingLotOccupancyDatagridFactory;
	}

	public function renderParkingLotOccupancy(string $id): void
	{
	}

	protected function createComponentParkingLotOccupancyDatagrid(): AdminDatagrid
	{
		return $this->parkingLotOccupancyDatagridFactory->create(UuidV4::fromString($this->processParameterStringId()));
	}
}
