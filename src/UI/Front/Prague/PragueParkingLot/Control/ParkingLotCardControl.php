<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueParkingLot\Control;

use App\Transport\Prague\Parking\ParkingLotOccupancyRepository;
use App\Transport\Prague\Parking\ParkingLotRepository;
use App\UI\Front\Base\BaseControl;

class ParkingLotCardControl extends BaseControl
{
	private ParkingLotRepository $parkingLotRepository;

	private ParkingLotOccupancyRepository $parkingLotOccupancyRepository;

	public function __construct(
		ParkingLotRepository $parkingLotRepository,
		ParkingLotOccupancyRepository $parkingLotOccupancyRepository
	) {
		$this->parkingLotRepository = $parkingLotRepository;
		$this->parkingLotOccupancyRepository = $parkingLotOccupancyRepository;
	}

	public function render(): void
	{
		$this->getTemplate()->lastUpdateTime = $this->parkingLotOccupancyRepository->getLastParkingDate();
		$this->getTemplate()->parkingLots = $this->parkingLotRepository->findAll();
		$this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
		$this->getTemplate()->render();
	}
}
