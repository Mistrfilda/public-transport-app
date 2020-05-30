<?php

declare(strict_types=1);

namespace App\UI\Front\Map;

use App\Transport\Prague\Vehicle\VehicleMapObjectProvider;
use App\UI\Front\FrontPresenter;
use App\UI\Shared\Map\MapControl;
use App\UI\Shared\Map\MapControlFactory;

class MapPresenter extends FrontPresenter
{
	private MapControlFactory $mapControlFactory;

	private VehicleMapObjectProvider $vehicleMapObjectProvider;

	public function __construct(
		MapControlFactory $mapControlFactory,
		VehicleMapObjectProvider $vehicleMapObjectProvider
	) {
		parent::__construct();
		$this->mapControlFactory = $mapControlFactory;
		$this->vehicleMapObjectProvider = $vehicleMapObjectProvider;
	}

	protected function createComponentMapControl(): MapControl
	{
		return $this->mapControlFactory->create($this->vehicleMapObjectProvider);
	}
}
