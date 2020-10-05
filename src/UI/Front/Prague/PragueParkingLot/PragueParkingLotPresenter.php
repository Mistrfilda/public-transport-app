<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueParkingLot;

use App\Transport\Prague\Parking\ParkingLotMapObjectProvider;
use App\UI\Front\FrontPresenter;
use App\UI\Shared\Map\MapControl;
use App\UI\Shared\Map\MapControlFactory;

class PragueParkingLotPresenter extends FrontPresenter
{
	private MapControlFactory $mapControlFactory;

	private ParkingLotMapObjectProvider $parkingLotMapObjectProvider;

	public function __construct(
		MapControlFactory $mapControlFactory,
		ParkingLotMapObjectProvider $parkingLotMapObjectProvider
	) {
		parent::__construct();
		$this->mapControlFactory = $mapControlFactory;
		$this->parkingLotMapObjectProvider = $parkingLotMapObjectProvider;
	}

	protected function createComponentMapControl(): MapControl
	{
		return $this->mapControlFactory->create($this->parkingLotMapObjectProvider);
	}
}
