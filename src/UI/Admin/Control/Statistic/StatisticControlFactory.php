<?php

declare(strict_types=1);

namespace App\UI\Admin\Control\Statistic;

interface StatisticControlFactory
{
	public function create(): StatisticControl;
}
