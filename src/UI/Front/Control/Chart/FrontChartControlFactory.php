<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Chart;

use App\UI\Shared\Statistic\Chart\IChartDataProvider;

interface FrontChartControlFactory
{
	public function create(string $type, IChartDataProvider $chartDataProvider): FrontChartControl;
}
