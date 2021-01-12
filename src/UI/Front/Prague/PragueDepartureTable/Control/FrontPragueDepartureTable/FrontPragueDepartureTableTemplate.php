<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable\Control\FrontPragueDepartureTable;

use App\Transport\Prague\DepartureTable\DepartureTable;
use App\Transport\Prague\StopLine\StopLine;
use App\UI\Front\Base\BaseControlTemplate;
use Nette\SmartObject;

/**
 * @method mixed clamp($value, $min, $max)
 */
class FrontPragueDepartureTableTemplate extends BaseControlTemplate
{
	use SmartObject;

	public bool $renderModal;

	/** @var StopLine[] */
	public array $stopLines;

	public DepartureTable $departureTable;

	public int $currentStep;

	public bool $showLoadMoreButton;
}
