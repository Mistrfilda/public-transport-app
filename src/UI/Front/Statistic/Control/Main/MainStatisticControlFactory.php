<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\Main;

interface MainStatisticControlFactory
{
    public function create(): MainStatisticControl;
}
