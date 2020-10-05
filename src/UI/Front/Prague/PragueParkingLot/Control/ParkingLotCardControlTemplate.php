<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueParkingLot\Control;

use App\Transport\Prague\Parking\ParkingLot;
use App\UI\Front\Prague\PragueParkingLot\PragueParkingLotPresenter;
use Nette\SmartObject;

/**
 * @method bool isLinkCurrent(string $destination = null, $args = [])
 * @method bool isModuleCurrent(string $module)
 */
class ParkingLotCardControlTemplate
{
	use SmartObject;

	public string $baseUrl;

	public string $basePath;

	/** @var string[] */
	public array $flashes;

	public ParkingLotCardControl $control;

	public PragueParkingLotPresenter $presenter;

	/** @var ParkingLot[] */
	public array $parkingLots;

	public string $lastUpdateTime;
}
