<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueParkingLot;

use App\Transport\Prague\Parking\ParkingLotMapObjectProvider;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Prague\PragueParkingLot\Control\ParkingLotCardControl;
use App\UI\Front\Prague\PragueParkingLot\Control\ParkingLotCardControlFactory;
use App\UI\Shared\Map\MapControl;
use App\UI\Shared\Map\MapControlFactory;

class PragueParkingLotPresenter extends FrontPresenter
{
	private MapControlFactory $mapControlFactory;

	private ParkingLotMapObjectProvider $parkingLotMapObjectProvider;

	private ParkingLotCardControlFactory $parkingLotCardControlFactory;

	public function __construct(
		MapControlFactory $mapControlFactory,
		ParkingLotMapObjectProvider $parkingLotMapObjectProvider,
		ParkingLotCardControlFactory $parkingLotCardControlFactory
	) {
		parent::__construct();
		$this->mapControlFactory = $mapControlFactory;
		$this->parkingLotMapObjectProvider = $parkingLotMapObjectProvider;
		$this->parkingLotCardControlFactory = $parkingLotCardControlFactory;
	}

	protected function createComponentMapControl(): MapControl
	{
		return $this->mapControlFactory->create($this->parkingLotMapObjectProvider);
	}

	protected function createComponentParkingLotCardControl(): ParkingLotCardControl
	{
		return $this->parkingLotCardControlFactory->create();
	}
}
