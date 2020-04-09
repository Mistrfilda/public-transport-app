<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Chart\Control;

use App\UI\Shared\Statistic\Chart\IChartDataProvider;

interface ChartControlFactory
{
	public function create(string $type, string $cardHeading, IChartDataProvider $chartDataProvider): ChartControl;
}
