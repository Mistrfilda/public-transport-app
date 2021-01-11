<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic;

use App\UI\Front\Base\BasePresenterTemplate;
use Nette\SmartObject;

/**
 * @method mixed clamp($value, $min, $max)
 */
class StatisticTemplate extends BasePresenterTemplate
{
	use SmartObject;

	public StatisticPresenter $presenter;
}
