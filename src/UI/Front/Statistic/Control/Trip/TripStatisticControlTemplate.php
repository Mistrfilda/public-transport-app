<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\Trip;

use App\UI\Front\Base\BaseControlTemplate;
use App\UI\Front\Statistic\FrontStatistic;
use Nette\SmartObject;

/**
 * @method mixed clamp($value, $min, $max)
 */
class TripStatisticControlTemplate extends BaseControlTemplate
{
	use SmartObject;

	/** @var FrontStatistic[] */
	public array $statistics;

	public string $tripId;
}
