<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueParkingLot\Control;

interface ParkingLotCardControlFactory
{
	public function create(): ParkingLotCardControl;
}
