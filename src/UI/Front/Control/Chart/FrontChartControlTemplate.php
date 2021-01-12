<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Chart;

use App\UI\Front\Base\BaseControlTemplate;
use Nette\SmartObject;

class FrontChartControlTemplate extends BaseControlTemplate
{
	use SmartObject;

	public string $chartId;

	public string $chartType;
}
