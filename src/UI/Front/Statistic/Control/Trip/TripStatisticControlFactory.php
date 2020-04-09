<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\Trip;

interface TripStatisticControlFactory
{
	public function create(string $tripId): TripStatisticControl;
}
