<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Chart\LineChart;

interface IChartDataProvider
{
    public function getChartData(): ChartData;
}
