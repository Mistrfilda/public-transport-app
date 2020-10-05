<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueParkingLot;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Prague\PragueParkingLot\Datagrid\ParkingLotDatagridFactory;

class PragueParkingLotPresenter extends AdminPresenter
{
	private ParkingLotDatagridFactory $parkingLotDatagridFactory;

	public function __construct(ParkingLotDatagridFactory $parkingLotDatagridFactory)
	{
		parent::__construct();
		$this->parkingLotDatagridFactory = $parkingLotDatagridFactory;
	}

	protected function createComponentParkingLotDatagrid(): AdminDatagrid
	{
		return $this->parkingLotDatagridFactory->create();
	}
}
