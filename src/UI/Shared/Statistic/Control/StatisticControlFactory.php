<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Control;

interface StatisticControlFactory
{
    public function create(): StatisticControl;
}
