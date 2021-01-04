<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\System;

interface SystemStatisticControlFactory
{
	public function create(): SystemStatisticControl;
}
