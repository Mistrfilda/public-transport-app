<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Modal;

interface TripStatisticModalRendererControlFactory
{
    public function create(string $tripId): TripStatisticModalRendererControl;
}
