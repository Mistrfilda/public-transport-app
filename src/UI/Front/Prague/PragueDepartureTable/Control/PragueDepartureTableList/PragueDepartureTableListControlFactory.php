<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList;

interface PragueDepartureTableListControlFactory
{
	public function create(): PragueDepartureTableListControl;
}
