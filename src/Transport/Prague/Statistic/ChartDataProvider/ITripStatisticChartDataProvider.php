<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\ChartDataProvider;

interface ITripStatisticChartDataProvider
{
	public function prepare(string $tripId): void;
}
