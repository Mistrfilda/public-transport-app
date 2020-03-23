<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\ChartDataProvider;

use App\UI\Shared\Statistic\Chart\LineChart\ChartData;
use App\UI\Shared\Statistic\Chart\LineChart\IChartDataProvider;

class TripStatisticChartDataProvider implements IChartDataProvider
{
    public function getChartData(): ChartData
    {
        $testik = new ChartData('labelik');

        $testik->add(
            'test 1',
            30
        );

        $testik->add(
            'test 2',
            40
        );

        $testik->add(
            'test 3',
            70
        );

        $testik->add(
            'test 4',
            10
        );

        return $testik;
    }
}
