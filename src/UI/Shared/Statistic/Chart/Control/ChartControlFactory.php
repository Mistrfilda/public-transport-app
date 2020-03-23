<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Chart\Control;

use App\UI\Shared\Statistic\Chart\LineChart\IChartDataProvider;

interface ChartControlFactory
{
    public function create(string $type, IChartDataProvider $chartDataProvider): ChartControl;
}
